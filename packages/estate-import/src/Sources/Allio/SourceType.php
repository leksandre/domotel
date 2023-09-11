<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Allio;

use Illuminate\Support\Facades\Log;
use Kelnik\EstateImport\Platform\Layouts\Settings\ReplacementListLayout;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Allio\Platform\SettingsLayout;
use Kelnik\EstateImport\Sources\Contracts\ClientBase;
use Kelnik\EstateImport\Sources\Contracts\HasClient;
use Kelnik\EstateImport\Sources\Contracts\SourceType as AbstractSourceType;
use Psr\Log\LoggerInterface;

final class SourceType extends AbstractSourceType implements HasClient
{
    public function getName(): string
    {
        return 'allio';
    }

    public function canBeScheduled(): bool
    {
        return true;
    }

    public function getConfig(): array
    {
        return [];
    }

    public function getPlatformLayouts(): array
    {
        return [
            SettingsLayout::class,
            ReplacementListLayout::class
        ];
    }

    public function getPreProcessor(): string
    {
        return PreProcessor::class;
    }

    /**
     * @param array|null $params
     * @param LoggerInterface|null $logger
     * @return \Kelnik\EstateImport\Sources\Allio\Contracts\AllioClient
     */
    public function getClient(?array $params = null, ?LoggerInterface $logger = null): ClientBase
    {
        $params ??= resolve(ImportSettingsService::class)->getSourceParams($this);
        $logger ??= Log::build(config('kelnik-estate-import.logging.config'));

        $config = resolve(Contracts\AllioConfig::class);
        $config->apiUrl = $params['client']['url'] ?? '';
        $config->apiLogin = $params['client']['login'] ?? '';
        $config->apiPassword = $params['client']['password'] ?? '';
        $config->apiDeveloper = (int)($params['client']['developer'] ?? 0);

        return resolve(
            Contracts\AllioClient::class,
            ['logger' => $logger, 'config' => $config]
        );
    }

    public function runImport(): void
    {
        $history = $this->createHistory();
        $className = $this->getPreProcessor();

        (new $className($history))->prepareData();
    }
}
