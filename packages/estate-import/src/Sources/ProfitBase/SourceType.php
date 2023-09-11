<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\ProfitBase;

use Illuminate\Support\Facades\Log;
use Kelnik\EstateImport\Platform\Layouts\Settings\ReplacementListLayout;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Contracts\ClientBase;
use Kelnik\EstateImport\Sources\Contracts\HasClient;
use Kelnik\EstateImport\Sources\Contracts\SourceType as AbstractSourceType;
use Kelnik\EstateImport\Sources\ProfitBase\Platform\SettingsLayout;
use Psr\Log\LoggerInterface;

final class SourceType extends AbstractSourceType implements HasClient
{
    public function getName(): string
    {
        return 'profitbase';
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
     * @return \Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseClient
     */
    public function getClient(?array $params = null, ?LoggerInterface $logger = null): ClientBase
    {
        $params ??= resolve(ImportSettingsService::class)->getSourceParams($this);
        $logger ??= Log::build(config('kelnik-estate-import.logging.config'));

        /** @var \Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseConfig $config */
        $config = resolve(\Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseConfig::class);
        $config->apiUrl = $params['client']['url'] ?? '';
        $config->apiKey = $params['client']['key'] ?? '';

        return resolve(
            \Kelnik\EstateImport\Sources\ProfitBase\Contracts\ProfitBaseClient::class,
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
