<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesFeature;

use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Platform\Layouts\PremisesFeature\ListLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureGroupRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = PremisesFeatureGroupRepository::class;
    protected int $priorityDefault = PremisesFeatureGroup::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.pfeature.edit';

    public function query(): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesFeatures');

        return [
            'list' => resolve($this->repository)->getAllWithFeatures(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.pfeature.sort'),
                [],
                false
            ),
            'coreService' => $this->coreService
        ];
    }

    public function layout(): array
    {
        return [
            ListLayout::class
        ];
    }
}
