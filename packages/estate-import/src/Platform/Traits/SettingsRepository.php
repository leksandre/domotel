<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Traits;

use Kelnik\EstateImport\Sources\Contracts\SourceType;

trait SettingsRepository
{
    private function getQueryData(SourceType $source): array
    {
        return [
            'coreService' => $this->coreService,
            'importSettingsService' => $this->importSettingsService,
            'importPlatformService' => $this->importPlatformService,
            'source' => $source,
            'sources' => $this->importSettingsService->getSourceList(),
            'settings' => [
                $source->getName() => $this->importSettingsService->getSourceParams($source)
            ]
        ];
    }
}
