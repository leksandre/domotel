<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\BelongsTo;
use Kelnik\EstateImport\Models\Proxy\Contracts\BelongsToAttachment;
use Kelnik\EstateImport\Models\Proxy\Contracts\HasMany;
use Kelnik\EstateImport\Repositories\Contracts\Estate\FloorRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesFeatureRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesStatusRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\PremisesTypeRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\SectionRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property PremisesRepository $repository */
final class Premises extends Contracts\EstateModelProxy implements BelongsTo, BelongsToAttachment, HasMany
{
    public const REF_FLOOR = 'floor';
    public const REF_SECTION = 'section';
    public const REF_STATUS = 'status';
    public const REF_TYPE = 'type';
    public const REF_FEATURES = 'features';
    public const REF_IMAGE_PLAN = 'image_plan';

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\Premises::class;
        $this->repository = resolve(PremisesRepository::class);
    }

    public function belongsArr(): array
    {
        return [
            'floor_id' => [
                'model' => \Kelnik\Estate\Models\Floor::class,
                'repository' => FloorRepository::class,
                'localField' => self::REF_FLOOR
            ],
            'section_id' => [
                'model' => \Kelnik\Estate\Models\Section::class,
                'repository' => SectionRepository::class,
                'localField' => self::REF_SECTION
            ],
            'status_id' => [
                'model' => PremisesStatus::class,
                'repository' => PremisesStatusRepository::class,
                'localField' => self::REF_STATUS,
                'callback' => [$this, 'setOriginalStatus']
            ],
            'type_id' => [
                'model' => PremisesType::class,
                'repository' => PremisesTypeRepository::class,
                'localField' => self::REF_TYPE,
                'callback' => [$this, 'setOriginalType']
            ]
        ];
    }

    public function attachmentsArr(): array
    {
        return [
            'image_plan_id' => [
                'localField' => self::REF_IMAGE_PLAN
            ]
        ];
    }

    public function hasManyArr(): array
    {
        return [
            self::REF_FEATURES => [
                'model' => PremisesFeature::class,
                'repository' => PremisesFeatureRepository::class,
                'proxy' => \Kelnik\EstateImport\Models\Proxy\PremisesFeature::class
            ]
        ];
    }

    public function setOriginalStatus(PremisesStatus $status): void
    {
        $this->data['original_status_id'] = $status->getKey();
    }

    public function setOriginalType(PremisesType $type): void
    {
        $this->data['original_type_id'] = $type->getKey();
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.premises');
    }

    public static function getSort(): int
    {
        return 1300;
    }
}
