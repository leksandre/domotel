<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Http\Controllers;

use Exception;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Kelnik\Core\Http\Controllers\BaseApiController;
use Kelnik\EstateSearch\Http\Resources\Filters\FilterResourceFactory;
use Kelnik\EstateSearch\Http\Resources\Filters\SettingsResource;
use Kelnik\EstateSearch\Http\Resources\PaginationResource;
use Kelnik\EstateSearch\Http\Resources\PremisesResource;
use Kelnik\EstateSearch\Services\Contracts\SearchConfigFactory;
use Kelnik\EstateSearch\Services\Contracts\SearchService;
use Symfony\Component\HttpFoundation\Response;

final class SearchController extends BaseApiController
{
    public const PARAM_REQUEST_TYPE = 'type';

    private SearchService $searchService;

    public function __construct()
    {
        $this->middleware([EncryptCookies::class, StartSession::class]);
    }

    public function __invoke(Request $request): JsonResponse
    {
        $requestType = $request->get(self::PARAM_REQUEST_TYPE);

        if ($requestType) {
            $requestType = 'request' . Str::ucfirst($requestType);
        }

        if ($requestType && method_exists($this, $requestType)) {
            try {
                $this->initSearchService();

                return call_user_func([$this, $requestType], $request);
            } catch (Exception $e) {
            }
        }

        return $this->sendError(['Bad request'], [], Response::HTTP_BAD_REQUEST);
    }

    private function initSearchService(): void
    {
        $componentId = Route::current()?->parameter('cid');

        if (!strlen($componentId) || !$config = resolve(SearchConfigFactory::class)->make($componentId)) {
            throw new InvalidArgumentException();
        }

        $this->searchService = resolve(SearchService::class, ['config' => $config]);
    }

    private function requestFilter(Request $request): JsonResponse
    {
        return $this->requestInit($request);
    }

    private function requestPagination(Request $request): JsonResponse
    {
        return $this->requestInit($request);
    }

    private function requestSort(Request $request): JsonResponse
    {
        return $this->requestInit($request);
    }

    private function requestInit(Request $request): JsonResponse
    {
        $requestData = $request->toArray();

        return $this->sendResponse($this->createResponse(
            $this->searchService->getForm($requestData),
            $this->getResults($requestData)
        ));
    }

    private function requestReset(Request $request): JsonResponse
    {
        return $this->sendResponse($this->createResponse(
            $this->searchService->getForm([]),
            $this->getResults([])
        ));
    }

    private function getSettings(Collection $data): JsonResource
    {
        return new SettingsResource(new Collection([
            'config' => $this->searchService->getConfig(),
            'hidePrice' => false,
            'sortOrder' => $data->get('sortOrder'),
        ]));
    }

    private function getFilters(Collection $borders, Collection $curData): Collection
    {
        return $borders
            ->map(static fn($el) => FilterResourceFactory::make($el['type'], $el, $curData[$el['name']]))
            ->values();
    }

    private function getResults(array $requestData): Collection
    {
        return $this->searchService->getConfig()?->pagination->type->returnAllResults()
            ? $this->searchService->getAllResults($requestData)
            : $this->searchService->getResults($requestData);
    }

    private function createResponse(Collection $form, Collection $results): array
    {
        return [
            'settings' => $this->getSettings($results),
            'filters' => $this->getFilters($form->get('baseBorders', []), $form->get('currentBorders', [])),
            'premises' => PremisesResource::collection($results->get('items')),
            'pagination' => $this->searchService->getConfig()->pagination->type->usePagination()
                ? new PaginationResource($results)
                : false
        ];
    }
}
