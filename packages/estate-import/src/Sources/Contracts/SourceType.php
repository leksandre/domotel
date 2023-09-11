<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Contracts;

use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\PreProcessor\Contracts\Filter;
use Kelnik\EstateImport\PreProcessor\Contracts\Mapper;
use Kelnik\EstateImport\Repositories\Contracts\HistoryRepository;
use Orchid\Screen\Layout;

abstract class SourceType
{
    public function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.sourceTypes.' . $this->getName());
    }

    public function canBeScheduled(): bool
    {
        return false;
    }

    protected function createHistory(): History
    {
        $history = new History();
        resolve(HistoryRepository::class)->save($history);

        return $history;
    }

    abstract public function getName(): string;

    abstract public function getConfig(): array;

    /**
     * @return string|null
     * @psalm-return class-string<Mapper>|null
     */
    public function getMapper(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     * @psalm-return class-string<Filter>|null
     */
    public function getFilter(): ?string
    {
        return null;
    }

    /** @return Layout[] */
    abstract public function getPlatformLayouts(): array;

    abstract public function runImport(): void;
}
