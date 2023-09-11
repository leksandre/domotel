<?php

declare(strict_types=1);

namespace Kelnik\Contact\View\Components\Offices;

use Artesaos\SEOTools\Facades\SEOMeta;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Contact\Models\Office;
use Kelnik\Contact\Providers\ContactServiceProvider;
use Kelnik\Contact\Repositories\Contracts\OfficeRepository;
use Kelnik\Contact\Services\Contracts\ContactService;
use Kelnik\Core\Map\Contracts\Map;
use Kelnik\Core\Map\MapFactory;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class Offices extends KelnikPageComponent implements HasContentAlias
{
    private ContactService $contactService;
    private PageService $pageService;

    public function __construct()
    {
        $this->contactService = resolve(ContactService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return ContactServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-contact::admin.components.offices.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-contact-offices';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $data = $this->getComponentData()->getValue();
        $content = $data?->get('content');
        $content['list'] = resolve(OfficeRepository::class)->getActive();
        $map = $this->createMap($content['list'], $data?->get('map', []) ?? []);

        if ($map) {
            $content['mapJson'] = base64_encode(
                json_encode([
                    'data' => $map->toArray()
                ])
            );
        }

        Cache::tags([
            $this->pageService->getPageComponentCacheTag($this->pageComponent->id),
            $this->contactService->getOfficeCacheTag()
        ])->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    private function createMap(Collection $offices, array $mapConfig): bool|Map
    {
        if ($offices->isEmpty()) {
            return false;
        }

        $mapFactory = new MapFactory();
        $map = $mapFactory->makeMap([
            'zoom' => $mapConfig['zoom'] ?? config('kelnik-contact.map.zoom')
        ]);

        $offices->each(static function (Office $el) use ($mapFactory, &$map) {
            if ($el->coords->lat && $el->coords->lng) {
                $map->addMarker($mapFactory->makeMarker([
                    'id' => $el->getKey(),
                    'title' => $el->title,
                    'description' => $el->address,
                    'image' => $el->image,
                    'icon' => asset('webicons/yandex-map/pin.svg'),
                    'coords' => $el->coords,
                    'type' => 'all',
                    'modifyClass' => 'object'
                ]));
            }
        });

        if ($map->getMarkers()->isEmpty()) {
            return false;
        }

        $map->setCenter($map->getMarkers()->first()->getCoords());

        return $map;
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();

        if (!isset($data['list']) || !$data['list'] instanceof Collection || $data['list']->isEmpty()) {
            return null;
        }

        $this->setMeta($data['list']->first());

        return view('kelnik-contact::components.offices.template', $data);
    }

    private function setMeta(Office $office): void
    {
        if (!$office->relationLoaded('image')) {
            return;
        }

        SEOMeta::addMeta('image', $office->image->url(), 'itemprop');
    }
}
