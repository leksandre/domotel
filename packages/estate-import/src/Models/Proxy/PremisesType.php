<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\BelongsTo;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesTypeGroupRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesTypeRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property PremisesTypeRepository $repository */
final class PremisesType extends Contracts\EstateModelProxy implements BelongsTo
{
    public const REF_GROUP = 'group';

    protected bool $updateExisting = false;

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\PremisesType::class;
        $this->repository = resolve(PremisesTypeRepository::class);
    }

    public function belongsArr(): array
    {
        return [
            'group_id' => [
                'model' => \Kelnik\Estate\Models\PremisesTypeGroup::class,
                'repository' => PremisesTypeGroupRepository::class,
                'localField' => self::REF_GROUP
            ]
        ];
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.premisesType');
    }

    public static function getSort(): int
    {
        return 1150;
    }
}
