<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Services\Contracts;

use Exception;
use Kelnik\Core\Repositories\Contracts\SettingsRepository;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\EstateImport\Sources\Contracts\SourceType;

interface ImportSettingsService
{
    public function __construct(SettingsService $settingsService, SettingsRepository $settingsRepository);

    /** @throws Exception */
    public function getSource(): SourceType;

    public function getSourceList(): array;

    public function saveSource(SourceType $source): bool;

    public function saveSourceParams(SourceType $source, array $params): bool;

    public function getSourceParams(SourceType $source): array;
}
