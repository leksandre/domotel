<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Xml;

use Illuminate\Support\Arr;
use Kelnik\EstateImport\Platform\Layouts\Settings\ReplacementListLayout;
use Kelnik\EstateImport\Services\Contracts\ImportSettingsService;
use Kelnik\EstateImport\Sources\Contracts\SourceType as AbstractSourceType;
use Kelnik\EstateImport\Sources\Xml\Platform\SourceListLayout;

final class SourceType extends AbstractSourceType
{
    public function getName(): string
    {
        return 'xml';
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
            SourceListLayout::class,
            ReplacementListLayout::class
        ];
    }

    public function getPreProcessor(): string
    {
        return RemoteFile::class;
    }

    public function getMapper(): ?string
    {
        return Mapper::class;
    }

    public function getFilter(): ?string
    {
        return Filter::class;
    }

    public function runImport(): void
    {
        /** @var  $settingsService */
        $settingsService = resolve(ImportSettingsService::class);

        $urls = Arr::get($settingsService->getSourceParams($this), 'list', []);

        if (!$urls) {
            return;
        }

        $preProcessorClass = $this->getPreProcessor();

        foreach ($urls as $url) {
            $history = $this->createHistory();

            (new $preProcessorClass($history))->prepareData(...$url);
        }
    }
}
