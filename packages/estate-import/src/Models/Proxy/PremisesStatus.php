<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesStatusRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property PremisesStatusRepository $repository */
final class PremisesStatus extends Contracts\EstateModelProxy
{
    protected bool $updateExisting = false;

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\PremisesStatus::class;
        $this->repository = resolve(PremisesStatusRepository::class);
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.premisesStatus');
    }

    public static function getSort(): int
    {
        return 1200;
    }
}
