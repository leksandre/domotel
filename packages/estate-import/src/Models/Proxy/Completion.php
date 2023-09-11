<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\Estate\CompletionRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property CompletionRepository $repository */
final class Completion extends Contracts\EstateModelProxy
{
    protected bool $updateExisting = false;

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\Completion::class;
        $this->repository = resolve(CompletionRepository::class);
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.completion');
    }

    public static function getSort(): int
    {
        return 600;
    }
}
