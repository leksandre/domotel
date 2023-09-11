<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Models\Proxy;

use Illuminate\Contracts\Filesystem\Filesystem;
use Kelnik\EstateImport\Models\History;
use Kelnik\EstateImport\Repositories\Contracts\Estate\ComplexRepository;
use Kelnik\EstateImport\Services\Contracts\CacheService;
use Psr\Log\LoggerInterface;

/** @property ComplexRepository $repository */
final class Complex extends Contracts\EstateModelProxy
{
    public function __construct(
        protected History $history,
        protected CacheService $cacheService,
        protected LoggerInterface $logger,
        protected Filesystem $storage
    ) {
        $this->namespace = \Kelnik\Estate\Models\Complex::class;
        $this->repository = resolve(ComplexRepository::class);
    }

    public static function getTitle(): string
    {
        return trans('kelnik-estate-import::admin.proxy.complex');
    }

    public static function getSort(): int
    {
        return 500;
    }
}
