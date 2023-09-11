<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Header;

use Closure;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Helpers\ImageHelper;
use Kelnik\Core\Helpers\PhoneHelper;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Form\View\Components\Form\FormDto;
use Kelnik\Menu\View\Components\Menu\MenuDto;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Header\Contracts\Template;
use Kelnik\Page\View\Components\Header\Enums\TemplateType;

final class Header extends KelnikPageComponent
{
    public const LOGO_HEIGHT_MIN = 20;
    public const LOGO_HEIGHT_MAX = 100;

    private CoreService $coreService;
    private SettingsService $settingsService;
    private PageService $pageService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->settingsService = resolve(SettingsService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return PageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-page::admin.components.header.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-header';
    }

    public static function getPageComponentSection(): string
    {
        return self::PAGE_COMPONENT_SECTION_HEADER;
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    protected function getTemplateData(): iterable
    {
        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = collect($this->getComponentData()->getValue()?->get('content') ?? []);
        $complexSettings = $this->settingsService->getComplex();

        $content->put('phone', $content->get('phone') ?: $complexSettings->get('phone') ?? '');
        $content->put('phoneLink', PhoneHelper::normalize($content->get('phone')));
        $content->put('homeLink', '/');
        $content->put('complexName', $complexSettings->get('name') ?? '');

        $logoLight = (int)$content->pull('logoLight') ?: (int)$complexSettings->get('logoLight');
        $logoDark = (int)$content->pull('logoDark') ?: (int)$complexSettings->get('logoDark');

        $files = [$logoLight, $logoDark];
        $files = array_filter($files);
        unset($complexSettings);

        if ($files) {
            $files = resolve(AttachmentRepository::class)->getByPrimary($files)->pluck(null, 'id');

            $logoLight = [
                'id' => $logoLight,
                'path' => '',
                'width' => 0,
                'height' => 0
            ];

            $logoDark = [
                'id' => $logoDark,
                'path' => '',
                'width' => 0,
                'height' => 0
            ];

            foreach (['logoLight', 'logoDark'] as $logo) {
                $fileData = $files[${$logo}['id']] ?? null;

                if (!$fileData) {
                    continue;
                }

                $storage = Storage::disk($fileData->disk);

                if (!$storage->exists($fileData->physicalPath())) {
                    continue;
                }

                ${$logo}['path'] = $fileData->url();

                try {
                    [${$logo}['width'], ${$logo}['height']] = ImageHelper::getImageSizes($fileData);
                // @codeCoverageIgnoreStart
                } catch (Exception $e) {
                // @codeCoverageIgnoreEnd
                }

                $content->put($logo, ${$logo});
            }
        }

        $menu = $content->pull('menu', []);
        $callbackButton = $content->pull('callbackButton', []);
        $logoHeight = (int)$content->get('logoHeight');

        if ($logoHeight === self::LOGO_HEIGHT_MIN) {
            $content->forget('logoHeight');
        }

        $complexSettingCacheTag = $this->settingsService->getCacheTag(
            CoreServiceProvider::MODULE_NAME,
            SettingsService::PARAM_COMPLEX
        );

        // Callback form
        if (!empty($callbackButton['form_id']) && $this->coreService->hasModule('form')) {
            $formParams = new FormDto();
            $formParams->primary = (int)$callbackButton['form_id'];
            $formParams->pageComponentId = (int)$this->pageComponent->getKey();
            $formParams->templateData['button_text'] = $callbackButton['text'];
            $formParams->buttonTemplate = 'kelnik-page::components.header.button';
            $content->put('callbackForm', $formParams);
        }

        // Menu
        if (!empty($menu) && $this->coreService->hasModule('menu')) {
            $templates = self::getMenuTemplates();
            $menuTemplates = [
                'desktop' => $templates->first(
                    static fn(HeaderMenuTemplate $el) => in_array(
                        $el->type,
                        [TemplateType::Desktop, TemplateType::Universal],
                        true
                    )
                ),
                'mobile' => $templates->first(
                    static fn(HeaderMenuTemplate $el) => $el->type === TemplateType::Mobile
                )
            ];
            unset($templates);

            $menuTypes = self::getMenuTemplates()->map(fn (Template $tmpl) => [
                'type' => $tmpl->type->value,
                'name' => $tmpl->name
            ])->pluck('type', 'name');

            foreach ($menu as $menuType => $v) {
                if (empty($v['id'])) {
                    continue;
                }
                $menuParams = new MenuDto();
                $menuParams->pageId = $this->page->id;
                $menuParams->pageComponentId = $this->pageComponent->id;
                $menuParams->primary = $v['id'];
                $menuParams->template = ($v['template'] ?? '') ?: ($menuTemplates[$menuType]?->name ?? null);
                $menuParams->templateData = [
                    'phone' => $content->get('phone'),
                    'phoneLink' => $content->get('phoneLink')
                ];
                $menuParams->cacheTags = [$complexSettingCacheTag];

                $menuTypeName = $menuTypes[$menuParams->template] ?? $menuType;

                $content->put('menu' . ucfirst($menuTypeName), $menuParams);
            }
        }

        Cache::tags([
            $this->pageService->getPageComponentCacheTag($this->pageComponent->id),
            $complexSettingCacheTag
        ])->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public static function getMenuTemplates(): Collection
    {
        return new Collection([
            new HeaderMenuTemplate(
                'kelnik-page::components.header.menu.basic',
                trans('kelnik-page::admin.components.header.menu.templates.basic')
            ),
            new HeaderMenuTemplate(
                'kelnik-page::components.header.menu.basicMobile',
                trans('kelnik-page::admin.components.header.menu.templates.basicMobile'),
                TemplateType::Mobile
            ),
            new HeaderMenuTemplate(
                'kelnik-page::components.header.menu.search',
                trans('kelnik-page::admin.components.header.menu.templates.search')
            ),
            new HeaderMenuTemplate(
                'kelnik-page::components.header.menu.searchMobile',
                trans('kelnik-page::admin.components.header.menu.templates.searchMobile'),
                TemplateType::Mobile
            ),
            new HeaderMenuTemplate(
                'kelnik-page::components.header.menu.multilevel',
                trans('kelnik-page::admin.components.header.menu.templates.multilevel')
            ),
            new HeaderMenuTemplate(
                'kelnik-page::components.header.menu.multilevelMobile',
                trans('kelnik-page::admin.components.header.menu.templates.multilevelMobile'),
                TemplateType::Mobile
            ),
            new HeaderMenuTemplate(
                'kelnik-page::components.header.menu.big',
                trans('kelnik-page::admin.components.header.menu.templates.big'),
                TemplateType::Universal
            ),
        ]);
    }

    public function render(): View|Closure|string
    {
        return view('kelnik-page::components.header.template', $this->getTemplateData());
    }
}
