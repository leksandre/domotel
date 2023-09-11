<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Footer;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Helpers\ImageHelper;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class Footer extends KelnikPageComponent
{
    private PageService $pageService;

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
        return trans('kelnik-page::admin.components.footer.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-footer';
    }

    public static function getPageComponentSection(): string
    {
        return self::PAGE_COMPONENT_SECTION_FOOTER;
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
        $logo = $this->getLogoInfo((int)($content->pull('logo') ?? 0));

        if ($logo) {
            $content->put('logo', $logo);
        }

        Cache::tags([
            $this->pageService->getPageComponentCacheTag($this->pageComponent->id)
        ])->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    private function getLogoInfo(int $logoId): false|array
    {
        if (!$logoId) {
            return false;
        }

        $attach = resolve(AttachmentRepository::class)->findByPrimary($logoId);

        if (!$attach->exists) {
            return false;
        }

        $logo = [
            'id' => $logoId,
            'path' => '',
            'width' => 0,
            'height' => 0
        ];

        $storage = Storage::disk($attach->disk);

        if (!$storage->exists($attach->physicalPath())) {
            return false;
        }

        $logo['path'] = $attach->url();

        try {
            [$logo['width'], $logo['height']] = ImageHelper::getImageSizes($attach);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        // @codeCoverageIgnoreEnd
        }

        return $logo;
    }

    public function render(): View|Closure|string
    {
        return view('kelnik-page::components.footer.template', $this->getTemplateData());
    }
}
