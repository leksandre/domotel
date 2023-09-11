<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Kelnik\Core\Platform\Layouts\Settings\BaseLayout;
use Kelnik\Core\Platform\Layouts\Settings\ColorsLayout;
use Kelnik\Core\Platform\Layouts\Settings\CookieNoticeLayout;
use Kelnik\Core\Platform\Layouts\Settings\FontsLayout;
use Kelnik\Core\Platform\Layouts\Settings\JsCodeLayout;
use Kelnik\Core\Platform\Layouts\Settings\MapLayout;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

final class SettingsScreen extends Screen
{
    protected readonly SettingsPlatformService $settingsPlatformService;
    protected readonly SettingsService $settingsService;

    public function __construct()
    {
        $this->settingsPlatformService = resolve(SettingsPlatformService::class);
        $this->settingsService = resolve(SettingsService::class);
    }

    public function query(): array
    {
        $this->name = trans('kelnik-core::admin.settings.title');

        /**
         * @var SettingsRepository $repo
         * @var Collection $settings
         */
        $repo = resolve(SettingsRepository::class);
        $settings = $repo->get(
            CoreServiceProvider::MODULE_NAME,
            [
                $this->settingsService::PARAM_COLORS,
                $this->settingsService::PARAM_FONTS,
                $this->settingsService::PARAM_MAP,
                $this->settingsService::PARAM_COMPLEX,
                $this->settingsService::PARAM_JS_CODES,
                $this->settingsService::PARAM_COOKIE_NOTICE
            ]
        );

        // Colors
        $defColors = $this->settingsService->getDefaultColors();
        $colors = $settings->first(fn ($el) => $el['name'] === SettingsService::PARAM_COLORS)?->value ?? collect();

        foreach ($defColors as $colorName => $colorValue) {
            $colors[$colorName] ??= $colorValue;
        }
        $colors = $this->settingsService->prepareColors($colors, collect($defColors));

        // Fonts
        $fonts = $settings->first(static fn($el) => $el['name'] === SettingsService::PARAM_FONTS)?->value
            ?? new Collection();

        if ($fonts->isEmpty()) {
            $fonts = collect($this->settingsService->getDefaultFonts());
        }
        $fonts = $this->settingsService->prepareFonts($fonts);

        return [
            'complex' => $settings->first(static fn($el) => $el['name'] === SettingsService::PARAM_COMPLEX)?->value,
            'colors' => $colors,
            'fonts' => $fonts,
            'map' => $settings->first(static fn($el) => $el['name'] === SettingsService::PARAM_MAP)?->value,
            'mapDragModeList' => $this->settingsPlatformService->getMapDragModeList(),
            'mapDragModeDefault' => $this->settingsService->getMapDragModeDefault(),
            'jsCode' => $settings->first(static fn($el) => $el['name'] === SettingsService::PARAM_JS_CODES)?->value,
            'cookieNotice' => $settings->first(
                static fn($el) => $el['name'] === SettingsService::PARAM_COOKIE_NOTICE
            )?->value
        ];
    }

    public function commandBar(): array
    {
        return [
            Button::make(trans('kelnik-core::admin.resetCache'))
                ->icon('bs.refresh')
                ->type(Color::WARNING())
                ->method('resetCache')
        ];
    }

    public function layout(): array
    {
        return [
            Layout::tabs([
                trans('kelnik-core::admin.settings.base.title') => BaseLayout::class,
                trans('kelnik-core::admin.settings.colors.title') => ColorsLayout::class,
                trans('kelnik-core::admin.settings.fonts.title') => FontsLayout::class,
                trans('kelnik-core::admin.settings.map.title') => MapLayout::class,
                trans('kelnik-core::admin.settings.jsCodes.title') => JsCodeLayout::class,
                trans('kelnik-core::admin.settings.cookieNotice.title') => CookieNoticeLayout::class
            ])
        ];
    }

    public function resetCache(): void
    {
        Artisan::call('optimize:clear');
        Artisan::call('view:clear');

        if (!App::isLocal()) {
            Artisan::call('optimize');
        }
        Artisan::call('view:cache');

        Toast::success(trans('kelnik-core::admin.model_saved_success'));
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        $resComplex = $this->settingsPlatformService->saveComplex(
            CoreServiceProvider::MODULE_NAME,
            $request->input('complex', [])
        );

        if ($resComplex instanceof RedirectResponse) {
            return $resComplex;
        }

        $resColor = $this->settingsPlatformService->saveColors(
            CoreServiceProvider::MODULE_NAME,
            $request->input('colors', [])
        );
        $resFont = $this->settingsPlatformService->saveFonts(
            CoreServiceProvider::MODULE_NAME,
            $request->file('fonts', []),
            $request->input('fonts', [])
        );

        $this->settingsPlatformService->saveMap(
            CoreServiceProvider::MODULE_NAME,
            $request->input('map', [])
        );
        $this->settingsPlatformService->saveJsCodes(
            CoreServiceProvider::MODULE_NAME,
            array_values($request->input('jsCode') ?? [])
        );
        $this->settingsPlatformService->saveCookieNotice(
            CoreServiceProvider::MODULE_NAME,
            $request->input('cookieNotice') ?? []
        );

        if ($resFont instanceof RedirectResponse) {
            return $resFont;
        }

        if ($resColor && $resFont && $resComplex) {
            Toast::success(trans('kelnik-core::admin.model_saved_success'));
        }

        return back();
    }
}
