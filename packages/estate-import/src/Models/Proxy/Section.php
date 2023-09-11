<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\BelongsTo;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BuildingRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\SectionRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property SectionRepository $repository */
final class Section extends Contracts\EstateModelProxy implements BelongsTo
{
    public const REF_BUILDING = 'building';

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\Section::class;
        $this->repository = resolve(SectionRepository::class);
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
        return trans('kelnik-estate-import::admin.proxy.section');
    }

    public static function getSort(): int
    {
        return 900;
    }
}
