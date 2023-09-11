<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Estate\Http\Middleware\RedirectToPdfController;
use Kelnik\Page\Models\Contracts\ComponentRouteProvider;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Services\Contracts\PageService;

final class RouteProvider extends ComponentRouteProvider
{
    public const PARAM_KEY = 'id';
    public const PARAM_PRINT = 'print';
    public const PRINT_TYPE_PDF = 'pdf';

    public function makeRoutesByParams(array $params): Collection
    {
        $prefix = Arr::get($params, 'prefix') ?? '';
        $prefix = $prefix ? trim($prefix) : '';
        $id = Arr::get($params, self::PARAM_KEY);

        $pageComponentRoute = $id
            ? resolve(PageService::class)->getPageComponentRoutesByPrimaryKey($id)
            : null;

        if (
            !$pageComponentRoute
            || ($pageComponentRoute->exists && !$pageComponentRoute->pageComponent->is($this->pageComponent))
        ) {
            $pageComponentRoute = new PageComponentRoute();
        }

        $pageComponentRoute->fill([
            'ignore_page_slug' => (bool)Arr::get($params, 'ignore_page_slug'),
            'path' => $prefix . '{' . self::PARAM_KEY . '}/{' . self::PARAM_PRINT . '?}',
            'middlewares' => [RedirectToPdfController::class],
            'params' => new Collection([
                self::PARAM_KEY => '[0-9]+',
                self::PARAM_PRINT => self::PRINT_TYPE_PDF
            ])
        ]);

        $pageComponentRoute->pageComponent()->associate($this->pageComponent);

        return new Collection([$pageComponentRoute]);
    }

    public function validateSavingRequest(Request $request): void
    {
        $request->validate([
            'routes.prefix' => 'nullable|regex:/[a-z0-9\-_]+/i',
            'routes.ignore_page_slug' => 'boolean'
        ]);
    }

    public function getPrefixFromPath(string $path): string
    {
        return mb_substr($path, 0, mb_strpos($path, '{' . self::PARAM_KEY . '}'));
    }
}
