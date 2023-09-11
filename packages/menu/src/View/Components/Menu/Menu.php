<?php

declare(strict_types=1);

namespace Kelnik\Menu\View\Components\Menu;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Menu\Models\MenuItem;
use Kelnik\Menu\Services\Contracts\MenuService;
use Kelnik\Menu\View\Components\Contracts\ComponentDto;
use Kelnik\Menu\View\Components\Contracts\MenuComponent;
use Kelnik\Page\Services\Contracts\PageService;

final class Menu extends MenuComponent
{
    protected const CACHE_TTL_DEFAULT = 864000; // 10 days

    protected DateTimeInterface|DateInterval|int $cacheTtl = self::CACHE_TTL_DEFAULT;

    private int|string $primary = 0;
    private int $pageComponentId = 0;
    private ?string $template = null;
    private array $templateData = [];
    private array $cacheTags = [];
    private readonly MenuService $menuService;
    private readonly PageService $pageService;
    public ?\Kelnik\Menu\Models\Menu $menu = null;

    public function __construct(?ComponentDto $params = null)
    {
        $props = ['primary', 'pageComponentId', 'template', 'templateData', 'cacheTags'];

        foreach ($props as $propertyName) {
            if (isset($params->{$propertyName})) {
                $this->{$propertyName} = $params->{$propertyName};
            }
        }

        $this->primary = (int)$this->primary;
        $this->menuService = resolve(MenuService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getAlias(): string
    {
        return 'kelnik-menu';
    }

    public function render(): View|Closure|string|null
    {
        $this->loadTemplateData();

        $res = \Illuminate\Support\Facades\View::first(
            [$this->template, self::getTemplates()->first()?->name],
            $this->templateData
        );

        if ($this->menu instanceof \Kelnik\Menu\Models\Menu) {
            $res->with('menu', $this->menuService->setSelectedItems($this->menu, url()->getRequest()));
        }

        return $res;
    }

    private function loadTemplateData(): void
    {
        if (!$this->primary) {
            return;
        }

        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res instanceof \Kelnik\Menu\Models\Menu) {
            $this->menu = $res;

            return;
        }

        $this->menu = $this->menuService->buildMenu($this->primary, url()->getRequest());

        if (!$this->menu?->exists) {
            return;
        }

        if ($this->template) {
            $this->menu->menuTemplate = $this->template;
        }

        $pageIds = $pageComponentIds = [];

        $this->menu->items->each(static function (MenuItem $menuItem) use (&$pageIds, &$pageComponentIds) {
            if ($menuItem['page_id']) {
                $pageIds[] = $menuItem['page_id'];
            }
            if ($menuItem['page_component_id']) {
                $pageComponentIds[] = $menuItem['page_component_id'];
            }
        });

        if ($this->pageComponentId) {
            $pageComponentIds[] = $this->pageComponentId;
        }

        $cacheTags = array_merge(
            [$this->menuService->getCacheTag($this->menu?->getKey() ?? 0)],
            $this->cacheTags
        );

        $cacheTagToService = [
            'pageIds' => [$this->pageService, 'getPageCacheTag'],
            'pageComponentIds' => [$this->pageService, 'getPageComponentCacheTag']
        ];

        foreach ($cacheTagToService as $arrName => $callback) {
            if (${$arrName}) {
                foreach (${$arrName} as $v) {
                    $cacheTags[$arrName . '_' . $v] = call_user_func($callback, $v);
                }
            }
        }

        $cacheTags = array_values($cacheTags);
        $cacheTags = array_filter($cacheTags);

        Cache::tags($cacheTags)->put($cacheId, $this->menu, $this->cacheTtl);
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new MenuTemplate(
                'kelnik-menu::components.menu.basic',
                trans('kelnik-menu::admin.components.menu.templates.basic')
            )
        ]);
    }

    public function getCacheId(): string
    {
        return $this->menuService->getCacheTag(
            md5(
                json_encode([
                    $this->primary,
                    $this->pageComponentId,
                    $this->templateData,
                    $this->template
                ])
            )
        );
    }
}
