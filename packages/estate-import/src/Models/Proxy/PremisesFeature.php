<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\BelongsTo;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureGroupRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property PremisesFeatureRepository $repository */
final class PremisesFeature extends Contracts\EstateModelProxy implements BelongsTo
{
    public const REF_GROUP = 'group';

    protected bool $updateExisting = false;
    protected bool $withoutEvents = false;

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\PremisesFeature::class;
        $this->repository = resolve(PremisesFeatureRepository::class);
    }

    public function belongsArr(): array
    {
        return [
            'group_id' => [
                'model' => PremisesFeatureGroup::class,
                'repository' => PremisesFeatureGroupRepository::class,
                'localField' => self::REF_GROUP
            ]
        ];
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.premisesFeature');
    }

    public static function getSort(): int
    {
        return 1050;
    }
}
