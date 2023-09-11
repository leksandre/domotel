<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Stub;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Kelnik\Core\Helpers\PhoneHelper;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class Stub extends KelnikPageComponent
{
    private readonly PageService $pageService;

    public function __construct()
    {
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return PageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-page::admin.components.stub.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-stub';
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

        $res = $this->getComponentData()->getValue();
        $content = collect($res->get('content') ?? []);
        $fileIds = [$content->get('logo'), $content->get('background')];
        $fileIds = array_filter($fileIds);

        if ($content->get('phone')) {
            $content->put('phoneLink', PhoneHelper::normalize($content->get('phone') ?? ''));
        }

        if ($fileIds) {
            $files = resolve(AttachmentRepository::class)->getByPrimary($fileIds);
            $content->put('logo', $files->first(static fn($el) => $el->id === (int)$content['logo']));
            $background = $files->first(static fn($el) => $el->id === (int)$content['background']);
            if ($background) {
                $background = Picture::init(new ImageFile($background))
                                ->setImageAttribute('alt', $background->alt ?? '')
                                ->render();
            }
            $content->put('background', $background);
        }

        $res->put('content', $content);

        Cache::tags($this->pageService->getPageComponentCacheTag($this->pageComponent->id))
            ->put($cacheId, $res, $this->cacheTtl);

        return $res;
    }

    public function render(): View|Closure|string
    {
        $data = $this->getTemplateData();
        $logo = $data->pull('content.logo');
        $background = $data->pull('content.background');

        return view('kelnik-page::components.stub', $data)
                ->with('logo', $logo)
                ->with('background', $background);
    }
}
