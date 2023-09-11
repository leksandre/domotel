<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Location;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\DeleteAttach;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Platform\Layouts\ModalGeocoderLayout;
use Kelnik\Page\View\Components\Location\Layouts\ContentLayout;
use Kelnik\Page\View\Components\Location\Layouts\MapLayout;
use Kelnik\Page\View\Components\Location\Layouts\MapMarkersLayout;
use Kelnik\Page\View\Components\Location\Layouts\MapMarkerTypesLayout;
use Kelnik\Page\View\Components\Location\Layouts\SettingsLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Layouts\Tabs;
use Orchid\Support\Facades\Layout;

final class DataProvider extends ComponentDataProvider
{
    use DeleteAttach;

    private function getMarkerTypes(): array
    {
        $markerTypes = Arr::get($this->data->get('map') ?? [], 'markerTypes', []);

        return Arr::pluck($markerTypes, 'title', 'code');
    }

    private function getMapCenter()
    {
        return Arr::get($this->data->get('map', []), 'center', []);
    }

    public function getCommandBar(array $commandBar): array
    {
        /** @var SettingsService $settingsService */
        $settingsService = resolve(SettingsService::class);
        $mapGlobalSettings = resolve(SettingsRepository::class);
        $mapGlobalSettings = $mapGlobalSettings->get(
            CoreServiceProvider::MODULE_NAME,
            $settingsService::PARAM_MAP
        )?->value;
        $mapService = $mapGlobalSettings->get('service', config('kelnik-core.map.service'));
        $mapCenter = $this->getMapCenter();
        $mapCenter = implode(',', $mapCenter);

        if ($mapService !== 'yandex' || !$this->getMarkerTypes() || !$mapCenter) {
            return $commandBar;
        }

        $serviceApiKey = Arr::get($mapGlobalSettings->get($mapService, []), 'api-search');

        $lastElement = array_pop($commandBar);
        $commandBar[] = Link::make('kelnik-page::admin.components.location.yandexMarkers.import')
                            ->set('data-controller', 'kelnik-geocoder')
                            ->set('data-action', 'kelnik-geocoder#openModal')
                            ->set('data-kelnik-geocoder-service', 'yandex')
                            ->set('data-kelnik-geocoder-api-key', $serviceApiKey)
                            ->set('data-kelnik-geocoder-center', $mapCenter)
                            ->set('data-kelnik-geocoder-prefix', 'markers')
                            ->set('data-kelnik-geocoder-modal', 'screen-modal-kelnik-geocoder-modal')
                            ->icon('bs.geo-alt')
                            ->turbo(false);

        if ($lastElement) {
            $commandBar[] = $lastElement;
        }

        return $commandBar;
    }

    public function getEditLayouts(): array
    {
        $buttons = Button::make(trans('kelnik-page::admin.save'))
                        ->icon('bs.save')
                        ->class('btn btn-secondary')
                        ->method('saveData');

        $markerTypes = $this->getMarkerTypes();
        $layouts = [
            trans('kelnik-page::admin.componentData.headers.content') => new ContentLayout($buttons),
            trans('kelnik-page::admin.componentData.headers.map') => new MapLayout($buttons),
            trans('kelnik-page::admin.componentData.headers.mapMarkerTypes') => new MapMarkerTypesLayout(
                $buttons
            ),
            trans('kelnik-page::admin.componentData.headers.mapMarkers') => new MapMarkersLayout($buttons),
            trans('kelnik-page::admin.componentData.headers.settings') => new SettingsLayout($buttons),
//                trans('kelnik-page::admin.componentData.headers.design') => new ThemeLayout(
//                    $this->getThemeFields(),
//                    $buttons
//                )
        ];

        $tabsLayout = new class ($layouts) extends Tabs {
            public function getSlug(): string
            {
                $objClone = clone $this;
                foreach ($objClone->layouts as &$layout) {
                    if ($layout instanceof MapMarkersLayout) {
                        $layout->layouts = [];
                    }
                }
                unset($layout);

                return sha1(json_encode($objClone));
            }
        };

        $layouts = [
            $tabsLayout
        ];

        if (!$markerTypes || !$this->getMapCenter()) {
            return $layouts;
        }

        $tmp = $markerTypes;
        $markerTypes = [];

        foreach ($tmp as $k => $v) {
            $k = base64_encode($k . '|' . urlencode($v ?? ''));
            $markerTypes[$k] = $v;
        }

        $layouts[] = (new ModalGeocoderLayout(
            'kelnik-geocoder-modal',
            [
                Layout::rows([
                    Select::make('types.')
                        ->title('kelnik-page::admin.components.location.marker.typesHeader')
                        ->options($markerTypes)
                        ->multiple()
                        ->required()
                        ->value(array_keys($markerTypes)),
                    Title::make('')->title('kelnik-page::admin.components.location.yandexMarkers.size'),
                    Group::make([
                        Input::make('spn[0]')
                            ->type('number')
                            ->step(0.01)
                            ->value(0.3),
                        Input::make('spn[1]')
                            ->type('number')
                            ->step(0.01)
                            ->value(0.3)
                    ]),
                    Switcher::make('rspn')
                        ->title('kelnik-page::admin.components.location.yandexMarkers.limitArea')
                        ->value(1),
                    Input::make('results')
                        ->title('kelnik-page::admin.components.location.yandexMarkers.resultCount')
                        ->type('number')
                        ->step(1)
                        ->value(10)
                        ->min(1)
                        ->max(50)
                ])
            ]
        ))->title(trans('kelnik-page::admin.components.location.addMarkers'))
            ->rawClick(true)
            ->size(Modal::SIZE_SM);

        return $layouts;
    }

    protected function getThemeFields(): Collection
    {
        return collect();
    }

    public function getComponentTitle(): string
    {
        return $this->data->get('content')['title'] ?? parent::getComponentTitle();
    }

    public function getContentFields(): Collection
    {
        return new Collection([
           [
               'name' => 'title',
               'type' => 'string',
               'validate' => 'nullable|max:255'
           ],
           [
               'name' => 'usp',
               'type' => 'array',
               'validate' => 'nullable|array'
           ],
           [
               'name' => 'alias',
               'type' => 'array',
               'validate' => 'nullable|max:150|regex:/[a-z0-9\-_]+/i'
           ]
        ]);
    }

    public function validateSavingRequest(PageComponent $pageComponent, Request $request): void
    {
        $validateRules = [];
        $this->getContentFields()->each(static function ($el) use (&$validateRules) {
            if (isset($el['validate'])) {
                $validateRules['data.content.' . $el['name']] = $el['validate'];
            }
        });

        $validateRules['data.map'] = 'array';
        $validateRules['data.map.zoom'] = 'numeric|min:0|max:16';
        $validateRules['data.map.markerTypes'] = 'nullable|array';
        $validateRules['markers'] = 'nullable|array';

        $request->validate($validateRules);
    }

    public function setDataFromRequest(PageComponent $pageComponent, Request $request): void
    {
        // Content
        self::$deleteAttachIds = [];
        $content = [];
        $newAttachIds = [];
        $markerTypes = [];

        foreach ($this->getContentFields() as $field) {
            $content[$field['name']] = $request->input('data.content.' . $field['name']);
        }

        if (isset($content['usp']) && is_array($content['usp'])) {
            $content['usp'] = array_map(function (array $el) use (&$newAttachIds) {
                $el = self::prepareMatrixElement($el);

                self::collectAttach($el, $newAttachIds);

                return $el;
            }, $content['usp']);
            $content['usp'] = array_values($content['usp']);
        }

        // Map
        $map = $request->input('data.map');

        $map['center'] = [
            'lat' => (float) $map['center']['lat'] ?? 0,
            'lng' => (float) $map['center']['lng'] ?? 0
        ];
        $map['zoom'] = (int)$map['zoom'] ?? 0;
        $map['route']['active'] = !empty($map['route']['active']);

        // Map marker types
        if (!empty($map['markerTypes'])) {
            $uniqAliases = [];
            $map['markerTypes'] = array_map(function (array $el) use (&$uniqAliases, &$newAttachIds, &$markerTypes) {
                $el = self::prepareMatrixElement($el);
                self::collectAttach($el, $newAttachIds);

                $oldCode = null;
                if (isset($el['code'])) {
                    $oldCode = $el['code'];
                }

                $el['code'] = Str::slug($el['title']);
                retry(5, function () use (&$el, &$uniqAliases) {
                    if (isset($uniqAliases[$el['code']])) {
                        $el['code'] .= rand(1, 50);
                    }
                });
                $markerTypes[$oldCode] = $el['code'];
                $uniqAliases[$el['code']] = $el['code'];

                return $el;
            }, $map['markerTypes']);
            $map['markerTypes'] = array_values($map['markerTypes']);
            unset($uniqAliases);
        }

        // Map markers
        $map['markers'] = [];
        $markers = $request->input('markers', []);

        if ($markers) {
            foreach ($markers as $typeName => $rows) {
                $hasType = $typeName !== MapMarkersLayout::INDEPENDENT_MARKER_GROUP;

                foreach ($rows as $row) {
                    if (empty($row['coords'])) {
                        $row['coords'] = '0,0';
                    }

                    if (count(explode(',', $row['coords'])) < 2) {
                        $row['coords'] .= ',0';
                    }

                    $row['title'] = $row['title']
                        ? trim($row['title'])
                        : trans('kelnik-page::admin.components.location.marker.titleDefault');

                    $marker = [
                        'coords' => $row['coords'],
                        'type' => $hasType ? $typeName : null,
                        'code' => $row['code'] ?? null,
                        'icon' => $row['icon'] ?? null,
                        'image' => $row['image'] ?? null,
                        'title' => $row['title'],
                        'description' => $row['description'] ?? null,
                    ];

                    // Save object marker
                    if (!$hasType) {
                        self::collectAttach($marker, $newAttachIds);
                        $map['markers'][] = $marker;

                        continue;
                    }

                    $marker['type'] = $markerTypes[$typeName] ?? null;

                    // Remove marker
                    if (!$marker['type']) {
                        continue;
                    }

                    // Save marker
                    $map['markers'][] = $marker;
                }
            }
        }

        $oldAttachIds = $this->getAttachIds($this->data);

        if ($oldAttachIds) {
            self::$deleteAttachIds = array_diff($oldAttachIds, $newAttachIds);
        }

        $this->data->put('content', $content);
        $this->data->put('map', $map);

        unset($content, $map);
    }

    public function setDefaultValue(): void
    {
        $this->data = new Collection([
            'content' => [
                'title' => $this->getComponentTitleOriginal()
            ]
        ]);
    }

    protected function getAttachIds(Collection $originData): array
    {
        $oldAttachIds = [];

        foreach (['content.usp', 'map.markerTypes', 'map.markers'] as $section) {
            $sectionData = Arr::get($originData, $section) ?? [];

            if (!$sectionData) {
                continue;
            }

            foreach ($sectionData as $row) {
                if (!$row) {
                    continue;
                }
                self::collectAttach($row, $oldAttachIds);
            }
        }

        return $oldAttachIds;
    }

    private static function prepareMatrixElement(array $el): array
    {
        $el['icon'] = (int)$el['icon'];

        if ($el['icon'] < 1) {
            $el['icon'] = null;
        }

        return $el;
    }

    private static function collectAttach(array $el, array &$resultArray): void
    {
        foreach (['icon', 'image'] as $fieldName) {
            if (!empty($el[$fieldName])) {
                $resultArray[(int)$el[$fieldName]] = (int)$el[$fieldName];
            }
        }
    }
}
