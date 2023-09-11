<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\View\Components\Selector;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\ComponentRouteProvider;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Services\Contracts\PageService;

final class RouteProvider extends ComponentRouteProvider
{
    public function makeRoutesByParams(array $params): Collection
    {
        $id = Arr::get($params, 'id');

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
            'path' => '{visualPath?}',
            'params' => new Collection([
                'visualPath' => '.*'
            ])
        ]);

        $pageComponentRoute->pageComponent()->associate($this->pageComponent);

        return new Collection([$pageComponentRoute]);
    }

    public function validateSavingRequest(Request $request): void
    {
        $request->validate([
            'routes.ignore_page_slug' => 'boolean'
        ]);
    }

    public function getPrefixFromPath(string $path): string
    {
        return '';
    }
}
