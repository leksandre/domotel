<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\BelongsTo;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BuildingRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\FloorRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property FloorRepository $repository */
final class Floor extends Contracts\EstateModelProxy implements BelongsTo
{
    public const REF_BUILDING = 'building';

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\Floor::class;
        $this->repository = resolve(FloorRepository::class);
    }

    public function belongsArr(): array
    {
        return [
            'building_id' => [
                'model' => \Kelnik\Estate\Models\Building::class,
                'repository' => BuildingRepository::class,
                'localField' => self::REF_BUILDING
            ]
        ];
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.floor');
    }

    public static function getSort(): int
    {
        return 800;
    }
}
