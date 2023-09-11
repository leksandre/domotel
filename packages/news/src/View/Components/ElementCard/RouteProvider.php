<?php

declare(strict_types=1);

namespace Kelnik\News\View\Components\ElementCard;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Kelnik\Page\Models\Contracts\ComponentRouteProvider;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Services\Contracts\PageService;

final class RouteProvider extends ComponentRouteProvider
{
    private const SLUG_NAME = 'slug';

    public function makeRoutesByParams(array $params): Collection
    {
        $prefix = Arr::get($params, 'prefix') ?? '';
        $prefix = $prefix ? trim($prefix) : '';
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
            'path' => $prefix . '{' . self::SLUG_NAME . '}',
            'params' => new Collection([self::SLUG_NAME => '[a-zA-Z0-9\-_]+'])
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
        return mb_substr($path, 0, mb_strpos($path, '{' . self::SLUG_NAME . '}'));
    }
}
