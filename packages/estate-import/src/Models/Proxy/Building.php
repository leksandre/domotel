<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\Estate\Models\Completion;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Models\Proxy\Contracts\BelongsTo;
use Kelnik\EstateImport\Repositories\Contracts\Estate\BuildingRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\CompletionRepository;
use Kelnik\EstateImport\Repositories\Contracts\Estate\ComplexRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property BuildingRepository $repository */
final class Building extends Contracts\EstateModelProxy implements BelongsTo
{
    public const REF_COMPLEX = 'complex';
    public const REF_COMPLETION = 'completion';

    protected bool $updateExisting = false;

    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\Building::class;
        $this->repository = resolve(BuildingRepository::class);
    }

    public function belongsArr(): array
    {
        return [
            'complex_id' => [
                'model' => \Kelnik\Estate\Models\Complex::class,
                'repository' => ComplexRepository::class,
                'localField' => self::REF_COMPLEX
            ],
            'completion_id' => [
                'model' => Completion::class,
                'repository' => CompletionRepository::class,
                'localField' => self::REF_COMPLETION
            ]
        ];
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.building');
    }

    public static function getSort(): int
    {
        return 700;
    }
}
