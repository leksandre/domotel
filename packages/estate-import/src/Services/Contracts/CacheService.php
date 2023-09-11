<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Services\Contracts;

use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\BaseLazyCollection;
use Orchid\Attachment\Models\Attachment;

interface CacheService
{
    public function __construct(History $history);

    public function hasModelList(string $modelNamespace): bool;

    public function cacheModelList(
        string $modelNamespace,
        BaseLazyCollection $repository,
        string $keyName = 'external_id'
    ): bool;

    public function getModel(string $modelNamespace, int|float|string $keyValue): null|EstateModel|Attachment;

    public function addModel(EstateModel|Attachment $model, string $keyName = 'external_id'): void;

    public function addStat(string $modelNamespace, string|int $event): void;

    public function getStat(): array;

    public function flush(): bool;
}
