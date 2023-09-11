<?php

declare(strict_types=1);

namespace Kelnik\FBlock\View\Components\BlockList;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\FBlock\Providers\FBlockServiceProvider;
use Kelnik\FBlock\Services\Contracts\BlockService;
use Kelnik\Form\View\Components\Form\FormDto;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\HasContentAlias;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Orchid\Attachment\Models\Attachment;

final class BlockList extends KelnikPageComponent implements HasContentAlias
{
    private CoreService $coreService;
    private BlockService $blockService;
    private SettingsService $settingsService;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
        $this->blockService = resolve(BlockService::class);
        $this->settingsService = resolve(SettingsService::class);
    }

    public static function getModuleName(): string
    {
        return FBlockServiceProvider::MODULE_NAME;
    }

    public static function getTitle(): string
    {
        return trans('kelnik-fblock::admin.components.blockList.title');
    }

    public static function getAlias(): string
    {
        return 'kelnik-fblock-block-list';
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

        $content = $this->getComponentData()->getValue()?->get('content');
        $content['template'] = $this->getComponentData()->getValue()?->get('template') ?? '';
        $content['template'] = self::getTemplates()->first(
            static fn(BlockTemplate $el) => $el->name === $content['template']
        ) ?? self::getTemplates()->first();

        /** @var BlockTemplate $template */
        $content['template'] = $content['template']->name;

        $content['blocks'] = $this->blockService->getBlockList();
        $content['blocks'] = $this->addCallbackForm($content['blocks']);
        $content['picture'] = null;
        $hasImage = !empty($content['image']);

        $colors = $this->settingsService->getCurrentColors();
        $content['colors'] = [
            'text' => $colors['brand-text'] ?? null,
            'primary' => $colors['brand-base'] ?? null
        ];

        if ($hasImage) {
            $content['image'] = resolve(AttachmentRepository::class)->findByPrimary($content['image']);
        }

        if (
            $hasImage
            && $content['image'] instanceof Attachment
            && strtolower($content['image']->getMimeType()) !== 'image/svg+xml'
            && $this->coreService->hasModule('image')
        ) {
            $content['picture'] = Picture::init(new ImageFile($content['image']))
                ->setLazyLoad(true)
                ->setBreakpoints([1440 => 578, 1280 => 520, 960 => 480, 670 => 360, 320 => 252])
                ->setImageAttribute('alt', $content['image']->alt ?? '')
                ->render();
        }

        Cache::tags([
            resolve(PageService::class)->getPageComponentCacheTag($this->pageComponent->id),
            $this->blockService->getCacheTag(),
            $this->settingsService->getCacheTag(
                CoreServiceProvider::MODULE_NAME,
                $this->settingsService::PARAM_COLORS
            )
        ])->put($cacheId, $content, $this->cacheTtl);

        return $content;
    }

    private function addCallbackForm(Collection $blocks): Collection
    {
        // @codeCoverageIgnoreStart
        if (!$this->coreService->hasModule('form')) {
            return $blocks;
        }
        // @codeCoverageIgnoreEnd

        foreach ($blocks as &$block) {
            if (!$block->button->getFormKey()) {
                continue;
            }
            $formParams = new FormDto();
            $formParams->primary = (int)$block->button->getFormKey();
            $formParams->pageComponentId = (int)$this->pageComponent->getKey();
            $formParams->templateData['button_text'] = $block->button->getText();
            $formParams->buttonTemplate = 'kelnik-fblock::components.blockList.partials.button';
            $formParams->cacheTags = [$this->blockService->getCacheTag()];
            $block->callbackForm = $formParams;
        }
        unset($block);

        return $blocks;
    }

    public static function initDataProvider(): ComponentDataProvider
    {
        return new DataProvider(self::class);
    }

    public function render(): View|Closure|string|null
    {
        $data = $this->getTemplateData();
        $template = $data['template'];
        $data['pageComponent'] = $this->pageComponent?->getKey() ?? 0;
        unset($data['template']);

        return view($template, $data);
    }

    public static function getTemplates(): Collection
    {
        return new Collection([
            new BlockTemplate(
                'kelnik-fblock::components.blockList.slider',
                trans('kelnik-fblock::admin.components.blockList.templates.slider')
            ),
            new BlockTemplate(
                'kelnik-fblock::components.blockList.cards-1',
                trans('kelnik-fblock::admin.components.blockList.templates.cards1')
            ),
            new BlockTemplate(
                'kelnik-fblock::components.blockList.cards-2',
                trans('kelnik-fblock::admin.components.blockList.templates.cards2')
            )
        ]);
    }
}
