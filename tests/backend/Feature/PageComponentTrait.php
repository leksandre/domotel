<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Kelnik\Core\Services\Contracts\SiteService;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageDynamicComponent;

trait PageComponentTrait
{
    private function createPage(): Page
    {
        return Page::factory()->createOne([
            'site_id' => $this->site->getKey(),
            'active' => true
        ]);
    }

    private function addComponentToPage(Model $page, string $componentNamespace): PageComponent
    {
        $pageComponent = PageComponent::factory()->makeOne([
            'active' => true,
            'component' => $componentNamespace
        ]);

        if (!$page->components()->save($pageComponent)) {
            throw new Exception('Can\'t associate component to page');
        }

        return $page->components()?->first() ?? $pageComponent;
    }

    private function addDynComponentToPage(Model $page, string $classNamespace): PageComponent
    {
        if (!is_a($classNamespace, KelnikPageDynamicComponent::class, true)) {
            throw new Exception('KelnikPageDynamicComponent required');
        }

        /** @var PageComponent $pageComponent */
        $pageComponent = PageComponent::factory()->makeOne([
            'active' => true,
            'component' => $classNamespace
        ]);

        if (!$page->components()->save($pageComponent)) {
            throw new Exception('Can\'t associate component to page');
        }

        $request = Request::createFromBase(new \Symfony\Component\HttpFoundation\Request());
        $routeProvider = $classNamespace::initRouteProvider($page, $pageComponent);

        $pageComponent->save();
        $pageComponent->routes()->saveMany($routeProvider->makeRoutesByParams($request));

        return $pageComponent;
    }
}
