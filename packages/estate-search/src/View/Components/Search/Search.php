<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\View\Components\Search;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\EstateSearch\Providers\EstateSearchServiceProvider;
use Kelnik\EstateSearch\Services\Contracts\SearchService;
use Kelnik\Form\View\Components\Form\FormDto;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class Search extends KelnikPageComponent
{
    public const VIEW_TYPE_TABLE = 1;
    public const VIEW_TYPE_CARD = 2;
    protected const CACHE_TTL = 864000; // 10 days

    private CoreService $coreService;
    private EstateService $estateService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->estateService = resolve(EstateService::class);
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public static function getModuleName(): string
    {
        return EstateSearchServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-search::admin.components.search.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-estate-search-form';
    }

    protected function getTemplateData(): array
    {
        $cacheId = $this->getCacheId();
        $data = Cache::get($cacheId);

        if ($data) {
            return $data;
        }

        $assets = $this->getAssets();
        $types = $this->getComponentData()->getValue()?->get('types') ?? [];
        $plural = explode('|', trans('kelnik-estate-search::front.pluralDefault'));

        if (count($types) === 1) {
            /** @var PremisesTypeGroup $type */
            $type = resolve(PremisesTypeGroupRepository::class)->findByPrimary(current($types));

            if ($type->exists && $type->plural) {
                $plural = $type->plural;
            }
            unset($type);
        }

        $data = [
            'title' => $this->getComponentData()->getValue()?->get('title'),
            'types' => encrypt(json_encode($types)),
            'statuses' => encrypt(json_encode($this->getComponentData()->getValue()?->get('statuses') ?? [])),
            'template' => $this->getComponentData()->getValue()?->get('template'),
            'assets' => [
                'css' => $assets->get('css'),
                'js' => $assets->get('js')
            ],
            'url' => route('kelnik.estateSearch.results', ['cid' => $this->pageComponent->getKey()], false),
            'baseUrl' => Route::current()->getCompiled()->getStaticPrefix(),
            'plural' => base64_encode(json_encode($plural))
        ];

        // Callback form
        $callbackForm = (int)$this->getComponentData()->getValue()?->get('form_id');

        if ($callbackForm && $this->coreService->hasModule('form')) {
            $formParams = new FormDto();
            $formParams->primary = $callbackForm;
            $formParams->pageComponentId = (int)$this->pageComponent->getKey();
            $formParams->slug = $this->getComponentData()->getPopupId();
            $formParams->template = 'kelnik-estate-search::form.booking';
            $data['callbackForm'] = $formParams;
        }

        Cache::tags([
            $this->estateService->getModuleCacheTag(),
            resolve(PageService::class)->getPageComponentCacheTag($this->pageComponent->getKey())
        ])->put($cacheId, $data, self::CACHE_TTL);

        return $data;
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();
        $template = $data['template'] ?? self::getTemplates()->first()?->name;

        unset($data['template']);

//        $service = $this->initSearchService($data);
//        $request = $this->request ?? request();
//
//        $data['form'] = $service->getForm($request->query());
//        $data['results'] = $service->getResults($request->query());

        return view($template, $data);
    }

    /** @codeCoverageIgnore  */
    private function getAssets(): Collection
    {
        $assets = config('kelnik-estate-search.assets');
        $res = [];

        if (!$assets) {
            return new Collection($res);
        }

        $excludeFolders = ['.', '..'];

        foreach ($assets as $section => $sectData) {
            if (!$sectData['path'] || !file_exists($sectData['path'])) {
                continue;
            }

            $files = array_diff(scandir($sectData['path']), $excludeFolders);

            if (!$files) {
                continue;
            }

            foreach ($files as $fileName) {
                $ext = explode('.', $fileName);
                $ext = end($ext);
                $ext = $ext ? mb_strtolower($ext) : '';

                if ($ext !== 'map') {
                    $res[$section][] = $sectData['url'] . '/' . $fileName;
                }
            }
        }

        return new Collection($res);
    }

//    private function initSearchService(array $data = []): SearchService
//    {
//        $service = resolve(SearchService::class);
//        $service->init($data);
//
//        return $service;
//    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new SearchTemplate(
                'kelnik-estate-search::components.search.simple',
                trans('kelnik-estate-search::admin.components.search.templates.simple')
            ),
        ]);
    }

    /** @codeCoverageIgnore  */
    public static function getViewTypes(): Collection
    {
        return new Collection([
            self::VIEW_TYPE_TABLE => trans('kelnik-estate-search::admin.components.search.view.table'),
            self::VIEW_TYPE_CARD => trans('kelnik-estate-search::admin.components.search.view.card')
        ]);
    }

    public function getCacheId(): string
    {
        return $this->estateService->getPremisesCacheTag('page_' . $this->page->getKey() . '_estate_search');
    }
}
