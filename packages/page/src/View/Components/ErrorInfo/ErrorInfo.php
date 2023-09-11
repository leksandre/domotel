<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\ErrorInfo;

use Artesaos\SEOTools\Facades\SEOMeta;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\View;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\PageStatusBufferDto;
use Kelnik\Page\Providers\PageServiceProvider;
use Kelnik\Page\Services\Contracts\HttpErrorService;
use Kelnik\Page\Services\Contracts\PageComponentBuffer;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\ErrorComponent;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;

final class ErrorInfo extends KelnikPageComponent implements ErrorComponent
{
    private HttpErrorService $httpErrorService;
    private PageService $pageService;
    private ?PageStatusBufferDto $buffer = null;

    public function __construct()
    {
        $this->httpErrorService = resolve(HttpErrorService::class);
        $this->pageService = resolve(PageService::class);
    }

    public static function getModuleName(): string
    {
        return PageServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-page::admin.components.errorInfo.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-page-error-info';
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    protected function getTemplateData(): iterable
    {
        $this->buffer = resolve(PageComponentBuffer::class)->get(PageStatusBufferDto::class);

        $cacheId = $this->getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $content = collect($this->getComponentData()->getValue()?->get('content') ?? []);
        $code = $this->getPageStatus();
        $text = $content->get('text')[$code] ?? [];
        $background = (int)$content->get('background');

        if ($background) {
            $background = resolve(AttachmentRepository::class)->getByPrimary([$background])->first();
        }

        if ($background && resolve(CoreService::class)->hasModule('image')) {
            $background = Picture::init(new ImageFile($background))
                ->setLazyLoad(true)
                ->setBreakpoints([
                    1920 => 2560,
                    1600 => 1919,
                    1440 => 1599,
                    1280 => 1439,
                    960 => 1279,
                    670 => 959,
                    320 => 669
                ])
                ->setImageAttribute('alt', $background->alt ?? '')
                ->render();
        }

        $content = new Collection([
            'code' => $code,
            'title' => $text['title'] ?? trans('kelnik-page::admin.components.errorInfo.state.' . $code . '.title'),
            'text' => $text['text'] ?? trans('kelnik-page::admin.components.errorInfo.state.' . $code . '.text'),
            'buttons' => array_filter($content->get('buttons') ?? [], fn($el) => !empty($el['title'])),
            'background' => $background
        ]);

        Cache::tags([
            $this->pageService->getPageComponentCacheTag($this->pageComponent->getKey())
        ])->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    public function render(): View|Closure|string
    {
        $data = $this->getTemplateData();

        SEOMeta::setTitle($data->get('title') ?? $this->page?->title ?? '');

        return view('kelnik-page::components.error-info', $data);
    }

    public function getCacheId(): string
    {
        return parent::getCacheId() . '_' . $this->getPageStatus();
    }

    private function getPageStatus(): int
    {
        return ($this->buffer?->status ?? 0) ?: $this->httpErrorService::DEFAULT_ERROR_CODE;
    }
}
