<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Menu\View\Components;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Kelnik\Menu\Models\Menu;
use Kelnik\Menu\Models\MenuItem;
use Kelnik\Menu\Services\Contracts\MenuService;
use Kelnik\Menu\View\Components\Menu\MenuDto;
use Kelnik\Page\View\Components\Usp\Usp;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class MenuTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use SiteTrait;

    private string $template = 'kelnik-menu::menu.template';

    protected function setUp(): void
    {
        parent::setUp();

        $this->initSite();
    }

    private function createMenu(): Menu
    {
        return Menu::factory()->hasItems(10)->createOne(['active' => true]);
    }

    public function testActiveMenuShowedOnPage()
    {
        $menu = $this->createMenu();

        $items = $menu->items;
        $activeElement = $items->first(static fn (MenuItem $el) => $el->isActive());

        if (!$activeElement) {
            $activeElement = $items->first();
            $activeElement->active = true;
            $activeElement->save();
        }

        $inActiveElement = $items->first(static fn (MenuItem $el) => !$el->isActive());

        $componentDto = new MenuDto();
        $componentDto->primary = $menu->getKey();

        $component = new \Kelnik\Menu\View\Components\Menu\Menu($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString($activeElement->title, $html);
        $this->assertStringNotContainsString($inActiveElement->title, $html);
    }

    public function testInactiveMenuNotShowedOnPage()
    {
        $menu = $this->createMenu();
        $menu->active = false;
        $menu->save();

        $componentDto = new MenuDto();
        $componentDto->primary = $menu->getKey();

        $component = new \Kelnik\Menu\View\Components\Menu\Menu($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringNotContainsString('navigation__list', $html);
    }

    public function testFakeMenuShouldNotBreakOutput()
    {
        $componentDto = new MenuDto();
        $componentDto->primary = 0;

        $component = new \Kelnik\Menu\View\Components\Menu\Menu($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringNotContainsString('navigation__list', $html);
    }

    public function testMenuElementLinkedToPageComponentShowedOnPage()
    {
        $faker = Factory::create(config('app.faker_locale'));
        $menu = $this->createMenu();
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, Usp::class);
        $uspData = [
            'content' => [
                'title' => $faker->company,
                'text' => $faker->realText(10),
                'alias' => $faker->slug
            ]
        ];
        $pageComponent->data->setValue(collect($uspData));
        $pageComponent->save();

        $menuElement = new MenuItem();
        $menuElement->title = $faker->sentence();
        $menuElement->active = true;
        $menuElement->page()->associate($page);
        $menuElement->pageComponent()->associate($pageComponent);
        $menuElement->menu()->associate($menu);
        $menuElement->save();

        $menuElement2 = new MenuItem();
        $menuElement2->title = $faker->sentence();
        $menuElement2->active = true;
        $menuElement2->page()->associate($page);
        $menuElement2->menu()->associate($menu);
        $menuElement2->save();

        $componentDto = new MenuDto();
        $componentDto->primary = $menu->getKey();

        $component = new \Kelnik\Menu\View\Components\Menu\Menu($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertTrue($menuElement->getUrl() === $page->getUrl() . '#' . $uspData['content']['alias']);
        $this->assertTrue($menuElement2->getUrl() === $page->getUrl());
        $this->assertStringContainsString($menuElement->getUrl(), $html);
        $this->assertStringContainsString($menuElement->title, $html);
        $this->assertStringContainsString($menuElement2->getUrl(), $html);
        $this->assertStringContainsString($menuElement2->title, $html);
    }

    public function testActiveMenuElementShowedOnPageUsingCache()
    {
        $menu = $this->createMenu();

        $componentDto = new MenuDto();
        $componentDto->primary = $menu->getKey();

        $cacheId = resolve(MenuService::class)->getCacheTag(md5(
            json_encode([$menu->getKey(), 0, [], null])
        ));

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($menu);
        Cache::swap($partialCacheMock);

        $component = new \Kelnik\Menu\View\Components\Menu\Menu($componentDto);
        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString(
            (string)$menu->items->first(static fn(MenuItem $el) => $el->isActive())?->title,
            $html
        );
    }
}
