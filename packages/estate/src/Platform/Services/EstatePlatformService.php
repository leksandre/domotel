<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Repositories\Contracts\BaseRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;

final class EstatePlatformService implements Contracts\EstatePlatformService
{
    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-estate::admin.estatePremises'))
            ->route(resolve(CoreService::class)->getFullRouteName('estate.complex.list'))
            ->icon('info')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px');
    }

    public function sortElements(BaseRepository $repository, array $elPriority, int $defaultPriority): bool
    {
        if (!$elPriority) {
            return false;
        }

        $elements = $repository->getByPrimary($elPriority);

        if ($elements->isEmpty()) {
            return false;
        }

        $elements->each(static function (EstateModel $el) use ($elPriority, $repository, $defaultPriority) {
            $el->priority = (int)array_search($el->getKey(), $elPriority) + $defaultPriority;
            $repository->save($el);
        });

        return true;
    }

    public function getElements(): array
    {
        return resolve(PremisesTypeGroupRepository::class)->getAllWithTypes()
            ->pluck('types')
            ->flatten(1)
            ->toArray();
    }
}
