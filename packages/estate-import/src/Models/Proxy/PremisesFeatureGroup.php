<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureGroupRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property PremisesFeatureGroupRepository $repository */
final class PremisesFeatureGroup extends Contracts\EstateModelProxy
{
    protected bool $updateExisting = false;

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\PremisesFeatureGroup::class;
        $this->repository = resolve(PremisesFeatureGroupRepository::class);
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.premisesFeatureGroup');
    }

    public static function getSort(): int
    {
        return 1000;
    }
}
