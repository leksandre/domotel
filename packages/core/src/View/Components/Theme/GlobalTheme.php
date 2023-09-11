<?php

declare(strict_types=1);

namespace Kelnik\Core\View\Components\Theme;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\View\View;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Theme\Contracts\Font;
use Kelnik\Core\View\Components\Contracts\KelnikComponentAlias;
use Kelnik\Core\View\Components\Contracts\KelnikComponentCache;

final class GlobalTheme extends Component implements KelnikComponentAlias, KelnikComponentCache
{
    public function __construct(
        protected readonly SettingsRepository $settingsRepository,
        protected readonly SettingsService $settingsService
    ) {
    }

    public static function getAlias(): string
    {
        return 'kelnik-core-global-theme';
    }

    protected function getTemplateData()
    {
        $cacheId = self::getCacheId();
        $res = Cache::get($cacheId);

        if ($res !== null) {
            return $res;
        }

        $res = [];
        $settings = $this->settingsRepository->get(
            CoreServiceProvider::MODULE_NAME,
            [
                $this->settingsService::PARAM_COLORS,
                $this->settingsService::PARAM_FONTS,
                $this->settingsService::PARAM_COMPLEX
            ]
        );

        if ($settings->isEmpty()) {
            Cache::forever($cacheId, $res);

            return $res;
        }

        /**
         * @var Collection $colors
         * @var Collection $fonts
         * @var Collection $complex
         */
        $colors = $settings->first(fn($el) => $el->name === $this->settingsService::PARAM_COLORS)?->value;
        $fonts = $settings->first(fn($el) => $el->name === $this->settingsService::PARAM_FONTS)?->value->filter();
        $complex = $settings->first(fn($el) => $el->name === $this->settingsService::PARAM_COMPLEX)?->value;

        if ($colors) {
            $colors = $this->settingsService->prepareColors($colors);
        }

        if ($fonts && $fonts->isNotEmpty()) {
            $fonts = $this->settingsService->prepareFonts($fonts);
            $fonts = $fonts->filter(static fn(Font $el) => $el->isLoaded() && $el->isActive());
        }

        $res = [
            'colors' => $colors,
            'fonts' => $fonts,
            'rounding' => (bool)Arr::get($complex?->get('rounding'), 'active', false)
        ];

        Cache::forever($cacheId, $res);

        return $res;
    }

    public function render(): View|string|null
    {
        return view('kelnik-core::components.global-theme', $this->getTemplateData());
    }

    public function getCacheId(): string
    {
        return 'theme';
    }
}
