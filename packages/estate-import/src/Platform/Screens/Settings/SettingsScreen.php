<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Screens\Settings;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateImport\Platform\Layouts\Settings\MainLayout;
use Kelnik\EstateImport\Platform\Listeners\ImportTypeListener;
use Kelnik\EstateImport\Platform\Services\Contracts\ImportPlatformService;
use Kelnik\EstateImport\Platform\Traits\SettingsRepository;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Contracts\HasClient;
use Orchid\Screen\Layout;
use Orchid\Screen\Screen;
use Throwable;

final class SettingsScreen extends Screen
{
    use SettingsRepository;

    protected ?string $name = null;

    public function __construct(
        private readonly CoreService $coreService,
        private readonly ImportPlatformService $importPlatformService,
        private readonly ImportSettingsService $importSettingsService
    ) {
    }

    public function query(): array
    {
        $this->name = trans('kelnik-estate-import::admin.menu.settings');

        try {
            return $this->getQueryData(
                $this->importSettingsService->getSource()
            );
        } catch (Throwable $throwable) {
        }

        return [];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            MainLayout::class,
            ImportTypeListener::class
        ];
    }

    public function addImportFile(Request $request): RedirectResponse
    {
        return $this->importPlatformService->addImportFile($request);
    }

    public function saveSourceList(Request $request): RedirectResponse
    {
        return $this->importPlatformService->saveSourceList($request);
    }

    public function saveReplacementList(Request $request): RedirectResponse
    {
        return $this->importPlatformService->saveReplacementList($request);
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        return $this->importPlatformService->saveSettings($request);
    }

    public function checkApiConnection(Request $request): array
    {
        $sourceName = $request->input('settings.source');
        $source = $this->importSettingsService->getSourceByName($sourceName);

        if (!$source instanceof HasClient) {
            return [
                'res' => false,
                'header' => 'header',
                'text' => 'text'
            ];
        }

        $res = $source->getClient($request->input('settings.' . $sourceName))->checkConnection();
        $block = $res ? 'success' : 'error';

        return [
            'res' => $res,
            'header' => trans('kelnik-estate-import::admin.apiCheck.' . $block . '.header'),
            'text' =>  trans('kelnik-estate-import::admin.apiCheck.' . $block . '.text')
        ];
    }
}
