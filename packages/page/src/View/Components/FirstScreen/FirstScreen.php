<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\FirstScreen;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Video\Factory;
use Kelnik\Core\Theme\Color;
use Kelnik\Estate\View\Components\StatList\StatListDto;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\News\View\Components\Element\ElementDto;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\FirstScreen\Contracts\Template;

final class FirstScreen extends KelnikPageComponent implements HasContentAlias
{
    private CoreService $coreService;
    private PageService $pageService;

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
        return trans('kelnik-page::admin.components.firstScreen.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-first-screen';
    }

    public function getContentAlias(): ?string
    {
        return Arr::get($this->getComponentData()->getValue()?->get('content'), 'alias');
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
        $content->put('animated', (int)$content->get('animated', 0) > 0);

        /** @var ?string $video */
        $video = $content->pull('video');
        $videoService = null;

        if ($video) {
            $videoService = Factory::make($video);
        }

        if ($videoService) {
            $content->put('video', [
                'id' => $videoService->getVideoId(),
                'preview' => $videoService->getThumb(),
                'name' => $videoService->getName(),
                'loopPlayer' => $videoService->getLoopPlayerLink()
            ]);
        }

        $template = $content->get('template');

        /** @var Template $template */
        $template = self::getTemplates()->first(static fn(Template $tmpl) => !$template || $tmpl->name === $template);

        $fileIds = (array) $content->pull('slider', []);
        $fileIds = array_filter($fileIds);

        /** @var AttachmentRepository $attachmentRepository */
        $attachmentRepository = resolve(AttachmentRepository::class);

        if ($fileIds) {
            $content->put('slider', $attachmentRepository->getByPrimary($fileIds));
            if ($this->coreService->hasModule('image')) {
                $slider = $content->get('slider')->map(
                    static fn($slider) => Picture::init(new ImageFile($slider))
                        ->setBreakpoints($template->imageBreakPoints)
                        ->render()
                );
                $content->put('slider', $slider);
            }
        }

        $content->put(
            'bgColor',
            new Color(
                'fs-bg',
                $content->pull('bgColor') ?? DataProvider::DEFAULT_BG_COLOR,
                DataProvider::DEFAULT_BG_COLOR
            )
        );

        $action = $content->pull('action');
        $action['id'] = !empty($action['id']) ? (int) $action['id'] : 0;

        if ($action['id'] && $this->coreService->hasModule('news')) {
            $action['icon'] = (int)($action['icon'] ?? 0);
            $actionParams = new ElementDto();
            $actionParams->pageId = $this->page->getKey();
            $actionParams->pageComponentId = $this->pageComponent->getKey();
            $actionParams->primary = $action['id'];
            $actionParams->templateData['buttonText'] = $action['buttonText'] ?? null;
            $actionParams->templateData['buttonLink'] = $action['buttonLink'] ?? null;
            $actionParams->template = $template?->actionTemplate;

            if ($action['icon'] && $icon = $attachmentRepository->findByPrimary($action['icon'])) {
                $actionParams->templateData['iconPath'] = $icon->url();
                $actionParams->templateData['iconBody'] = Storage::disk($icon->disk)->get($icon->physicalPath());
                unset($icon);
            }

            $content->put('actionParams', $actionParams);
        }

        $estateTypes = Arr::get($content->pull('estate'), 'types', []);
        $estateTypes = array_filter($estateTypes, static fn($el) => $el['active'] ?? false);

        if ($estateTypes && $this->coreService->hasModule('estate')) {
            $estateParams = new StatListDto();
            $estateParams->types = $estateTypes;
            $estateParams->pageId = $this->page->getKey();
            $estateParams->pageComponentId = $this->pageComponent->getKey();
            $estateParams->template = $template?->estateTemplate;

            $content->put('estateParams', $estateParams);
            unset($estateParams);
        }

        Cache::tags($this->pageService->getPageComponentCacheTag($this->pageComponent->id))
            ->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string
    {
        $data = $this->getTemplateData();
        $slider = $data->pull('slider');
        $bgColor = $data->pull('bgColor');
        $template = $data->pull('template') ?? self::getTemplates()->first()->name;

        return view($template, $data)->with('slider', $slider)->with('bgColor', $bgColor);
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new FirstScreenTemplate(
                'kelnik-page::components.firstScreen.basic.template',
                trans('kelnik-page::admin.components.firstScreen.templates.basic'),
                '/vendor/kelnik-page/icons/first-screen/base.svg',
                'kelnik-page::components.firstScreen.basic.action',
                'kelnik-page::components.firstScreen.basic.statList',
                [1920 => 2560, 1600 => 1919, 1440 => 1599, 1280 => 1439, 960 => 1279, 670 => 959, 320 => 669]
            ),
            new FirstScreenTemplate(
                'kelnik-page::components.firstScreen.alternative.template',
                trans('kelnik-page::admin.components.firstScreen.templates.alternative'),
                '/vendor/kelnik-page/icons/first-screen/alternative.svg',
                'kelnik-page::components.firstScreen.alternative.action',
                'kelnik-page::components.firstScreen.alternative.statList',
                [1920 => 2417, 1600 => 1813, 1440 => 1511, 1280 => 1360, 960 => 1208, 670 => 906, 320 => 631]
            ),
            new FirstScreenTemplate(
                'kelnik-page::components.firstScreen.colored.template',
                trans('kelnik-page::admin.components.firstScreen.templates.colored'),
                '/vendor/kelnik-page/icons/first-screen/white-bg.svg',
                'kelnik-page::components.firstScreen.colored.action',
                'kelnik-page::components.firstScreen.colored.statList',
                [1920 => 2560, 1600 => 1919, 1440 => 1599, 1280 => 1439, 960 => 1279, 670 => 959, 320 => 669]
            )
        ]);
    }
}
