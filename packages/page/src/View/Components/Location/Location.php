<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Location;

use Closure;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Map\Contracts\Coords;
use Kelnik\Core\Map\Contracts\Map;
use Kelnik\Core\Map\MapFactory;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Params;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Orchid\Attachment\Models\Attachment;

final class Location extends KelnikPageComponent implements HasContentAlias
{
    private const ICON_USP_WIDTH = 96;
    private const ICON_USP_HEIGHT = 96;
    private const ICON_DEFAULT_WIDTH = 56;
    private const ICON_DEFAULT_HEIGHT = 56;
    private readonly CoreService $coreService;
    private readonly PageService $pageService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return PageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-page::admin.components.location.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-location';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    /** @throws FileNotFoundException */
    protected function getTemplateData(): iterable
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = collect($this->getComponentData()->getValue()?->get('content') ?? []);
        $mapData = $this->getComponentData()->getValue()?->get('map') ?? [];

        $usp = $content->pull('usp') ?? [];
        $markerTypes = $mapData['markerTypes'] ?? [];
        $markers = $mapData['markers'] ?? [];
        $markerTypes = self::filterMarkerTypes($markerTypes, $markers);

        $fileIds = array_merge(
            array_column($usp, 'icon'),
            array_column($markerTypes, 'icon'),
            array_column($markers, 'icon'),
            array_column($markers, 'image')
        );
        $fileIds = array_filter($fileIds);
        $images = collect();

        if ($fileIds) {
            $images = resolve(AttachmentRepository::class)->getByPrimary($fileIds);
        }

        $typeIcons = [];
        $hasModuleImage = $this->coreService->hasModule('image');

        foreach (['usp', 'markerTypes', 'markers'] as $arrName) {
            ${$arrName} = array_map(static function (array $el) use ($images, $arrName, &$typeIcons, $hasModuleImage) {
                $el['icon'] = $images->first(fn($attach) => self::getAttachById($attach, (int)$el['icon']));

                if ($el['icon']) {
                    if ($arrName === 'markerTypes') {
                        $typeIcons[$el['code']] = $el['icon'];
                    }

                    if (in_array($arrName, ['markerTypes', 'usp'])) {
                        $el['iconBody'] = null;
                        $isUsp = $arrName === 'usp';
                        $isSvg = strtolower($el['icon']->extension) === 'svg';

                        if ($isUsp && $isSvg) {
                            $el['iconPath'] = $el['icon']->url();
                            return $el;
                        }

                        if ($isSvg) {
                            $storage = Storage::disk($el['icon']->disk);
                            $el['iconBody'] = $storage->exists($el['icon']->physicalPath())
                                                ? $storage->get($el['icon']->physicalPath())
                                                : '';
                        } else {
                            $imageFile = new ImageFile($el['icon']);
                            $params = new Params($imageFile);
                            $params->width = $isUsp ? self::ICON_USP_WIDTH : self::ICON_DEFAULT_WIDTH;
                            $params->height = $isUsp ? self::ICON_USP_HEIGHT : self::ICON_DEFAULT_HEIGHT;
                            $el['iconPath'] = Picture::getResizedPath($imageFile, $params);
                            unset($imageFile, $params);
                        }
                    }
                }

                if (array_key_exists('image', $el)) {
                    $el['image'] = $images->first(
                        fn($attach) => self::getAttachById($attach, (int)$el['image'])
                    );
                }

                return $el;
            }, ${$arrName});
        }
        unset($arrName, $images, $fileIds, $hasModuleImage);

        $map = $this->createMap($mapData, $markers, $typeIcons);

        $mapData = [
            'route' => $mapData['route'] ?? [],
            'types' => $markerTypes,
            'json' => $map
                        ? base64_encode(json_encode(['data' => $map->toArray()]))
                        : null
        ];
        $content->put('map', $mapData);
        $content->put('usp', $usp);
        unset($mapData, $usp, $markerTypes, $markers, $mapService, $mapApiKey);

        Cache::tags($this->pageService->getPageComponentCacheTag($this->pageComponent->id))
            ->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string
    {
        return view('kelnik-page::components.location', $this->getTemplateData());
    }

    private function createMap(array $mapData, array $markers, array $typeIcons): ?Map
    {
        if (!$markers) {
            return null;
        }

        $mapFactory = new MapFactory();
        $map = $mapFactory->makeMap([
            'center' => !empty($mapData['center'])
                ? resolve(Coords::class, [
                    'lat' => $mapData['center']['lat'],
                    'lng' => $mapData['center']['lng']
                ])
                : null,
            'zoom' => $mapData['zoom'] ?? config('kelnik-core.map.zoom')
        ]);

        foreach ($markers as $marker) {
            if (empty($marker['icon']) && !empty($marker['type'])) {
                $marker['icon'] = Arr::get($typeIcons, $marker['type']);
            }
            $map->addMarker($mapFactory->makeMarker($marker));
        }

        return $map;
    }

    /**
     * Removes empty marker types
     *
     * @param $markerTypes
     * @param $markers
     *
     * @return array
     */
    private static function filterMarkerTypes($markerTypes, $markers): array
    {
        $defRes = [];

        if (!$markers) {
            return $defRes;
        }

        $markerTypesEnabled = array_column($markers, 'type');
        $markerTypesEnabled = array_unique($markerTypesEnabled);
        $markerTypesEnabled = array_filter($markerTypesEnabled);

        if (!$markerTypesEnabled) {
            return $defRes;
        }

        return array_filter($markerTypes, static fn($el) => in_array($el['code'], $markerTypesEnabled));
    }

    private static function getAttachById(Attachment $attach, int $requiredId): bool
    {
        return $attach->id === $requiredId;
    }
}
