<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Platform\Services\Contracts\EstatePlatformService;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

abstract class BaseScreen extends Screen
{
    protected string $repository;
    protected int $priorityDefault = EstateModel::PRIORITY_DEFAULT;
    protected string $routeToEdit = '';
    protected string $routeToList = '';
    protected ?string $name = null;
    protected CoreService $coreService;
    protected EstatePlatformService $estatePlatformService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->estatePlatformService = resolve(EstatePlatformService::class);
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName($this->routeToEdit))
        ];
    }

    public function sortable(Request $request): JsonResponse
    {
        $fields = array_values($request->input('elements', []));

        if (!$fields) {
            return Response::json([
                'success' => false,
                'messages' => [trans('kelnik-estate::admin.error.emptyList')]
            ]);
        }

        foreach ($fields as $k => &$v) {
            $v = (int) $v;
            if (!$v) {
                unset($fields[$k]);
            }
        }

        $this->estatePlatformService->sortElements(resolve($this->repository), $fields, $this->priorityDefault);

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-estate::admin.success')]
        ]);
    }
}
