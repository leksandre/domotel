<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Kelnik\Core\Models\Setting;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\EstateImport\Providers\EstateImportServiceProvider;
use Kelnik\EstateImport\Sources\Contracts\SourceType;

final class ImportSettingsService implements Contracts\ImportSettingsService
{
    private const VALUE_KEY = 'data';

    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly SettingsRepository $settingsRepository
    ) {
    }

    public function getSource(): SourceType
    {
        $setting = $this->settingsService->getCached(EstateImportServiceProvider::MODULE_NAME, 'source')?->value;
        $sourceName = '';

        if ($setting instanceof Collection && $setting->isNotEmpty()) {
            $sourceName = (string)$setting->get(self::VALUE_KEY, '');
        }

        $sources = $this->getSourceList();

        if (!$sources) {
            throw new Exception('Source types list is empty');
        }

        if (!isset($sources[$sourceName])) {
            $sourceName = array_key_first($sources);
        }

        return $this->getSourceByName($sourceName);
    }

    /** @throws Exception */
    public function getSourceByName(string $sourceName): SourceType
    {
        foreach ($this->getSources() as $sourceClassName) {
            $sourceClass = new $sourceClassName();

            if ($sourceClass->getName() === $sourceName) {
                return $sourceClass;
            }
        }

        throw new Exception('Source type "' . $sourceName . '" does not exists');
    }

    public function getSourceList(): array
    {
        $types = [];

        foreach ($this->getSources() as $sourceClassName) {
            $sourceClass = new $sourceClassName();
            $types[$sourceClass->getName()] = $sourceClass->getTitle();
        }

        return $types;
    }

    /** @return array<class-string> */
    private function getSources(): array
    {
        return config('kelnik-estate-import.source', []);
    }

    public function saveSource(SourceType $source): bool
    {
        $setting = $this->getSettingValue('source');
        $setting->value = new Collection([self::VALUE_KEY => $source->getName()]);

        $res = $this->settingsRepository->set($setting);

        if (!$res) {
            return false;
        }

        return $this->settingsService->resetSettingCache(EstateImportServiceProvider::MODULE_NAME, 'source');
    }

    public function getSourceParams(SourceType $source): array
    {
        /** @var ?Collection $setting */
        $setting = $this->settingsService->getCached(
            EstateImportServiceProvider::MODULE_NAME,
            $source->getName()
        )?->value;

        if (!($setting instanceof Collection) || $setting->isEmpty()) {
            return [];
        }

        $value = (string)$setting->get(self::VALUE_KEY, '');

        return strlen($value)
            ? (array)Crypt::decrypt($value)
            : [];
    }

    public function saveSourceParams(SourceType $source, array $params): bool
    {
        $setting = $this->getSettingValue($source->getName());
        $setting->value = new Collection([self::VALUE_KEY => $params ? Crypt::encrypt($params) : '']);

        $res = $this->settingsRepository->set($setting);

        if (!$res) {
            return false;
        }

        return $this->settingsService->resetSettingCache(EstateImportServiceProvider::MODULE_NAME, $source->getName());
    }

    private function getSettingValue(string $name): Setting
    {
        $moduleName = EstateImportServiceProvider::MODULE_NAME;
        $setting = $this->settingsRepository->get($moduleName, $name) ?? new Setting();

        if (!$setting->exists) {
            $setting->module = $moduleName;
            $setting->name = $name;
        }

        return $setting;
    }
}
