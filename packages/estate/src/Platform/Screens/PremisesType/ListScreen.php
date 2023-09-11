<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesType;

use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Platform\Layouts\PremisesType\ListLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = PremisesTypeGroupRepository::class;
    protected int $priorityDefault = PremisesTypeGroup::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.ptype.edit';

    public function query(): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesTypes');

        return [
            'list' => resolve($this->repository)->getAllWithTypes(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.ptype.sort'),
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
