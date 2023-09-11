<?php

declare(strict_types=1);

namespace Kelnik\Core\Services;

use Illuminate\Bus\Batch;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Kelnik\Core\Dto\ClearingDto;
use Kelnik\Core\Enums\ClearingResultType;
use Kelnik\Core\Models\KelnikModuleInfo;
use Kelnik\Core\Providers\CoreServiceProvider;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Throwable;

final class CoreService implements Contracts\CoreService
{
    public function getFullRouteName(string $routeName): string
    {
        return config('kelnik-core.routeNamePrefix.platform') . $routeName;
    }

    public function getModuleList(): Collection
    {
        $app = app();
        $provider = new CoreServiceProvider($app);
        $res = collect();

        $components = [];
        resolve(BladeComponentRepository::class)
            ->getList()
            ->each(
                static function (ComponentDataProvider $value, $key) use (&$components) {
                    $components[$value->getModuleName()][$key] = $value->getComponentTitleOriginal();
                }
            );

        foreach ($provider->getModules() as $provider) {
            $name = $provider->getName();
            $res->add(new KelnikModuleInfo(
                $provider::class,
                $name,
                $provider->getTitle(),
                $provider::VERSION,
                $provider->hasCleaner(),
                $components[$name] ?? []
            ));
        }

        return $res;
    }

    public function hasModule(string $moduleName): bool
    {
        if (!strlen($moduleName)) {
            return false;
        }

        return $this->getModuleList()->contains(static fn(KelnikModuleInfo $el) => $el->getName() === $moduleName);
    }

    public function getModuleNameByClassNamespace(string $classNamespace): string
    {
        $classNamespace = explode('\\', $classNamespace);

        return !empty($classNamespace[1]) ? Str::lower($classNamespace[1]) : '';
    }

    /** @throws Throwable */
    public function clearingModules(ClearingDto $dto): ClearingResultType
    {
        $isPending = config('queue.default') !== 'sync';
        $modules = $this->getModuleList();
        /** @var Dispatchable[] $jobs */
        $jobs = [];

        $app = app();

        /** @var KelnikModuleInfo $module */
        foreach ($modules as $module) {
            if (in_array($module->getName(), $dto->modules)) {
                $providerClassName = $module->getProvider();
                $jobs = array_merge(
                    $jobs,
                    (new $providerClassName($app))->getCleanerJobs()
                );
            }
        }

        if (!$isPending) {
            array_map(fn($job) => $job::dispatch(), $jobs);
            return ClearingResultType::Sync;
        }

        Bus::batch(array_map(fn(string $job) => new $job(), $jobs))
            ->allowFailures()
            ->finally(function (Batch $batch) use ($dto, $isPending) {
                if ($isPending && $dto->notification && class_exists($dto->notification)) {
                    $total = $batch->totalJobs;
                    $failed = $batch->failedJobs;

                    $dto->user->notify(new $dto->notification($total - $failed, $failed));
                }
            })
            ->dispatch();

        return ClearingResultType::Queue;
    }
}
