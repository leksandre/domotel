<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesStatus;

use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Platform\Layouts\PremisesStatus\ListLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = PremisesStatusRepository::class;
    protected int $priorityDefault = PremisesStatus::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.pstatus.edit';

    public function query(): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesStatuses');

        return [
            'list' => resolve($this->repository)->getAllForAdmin(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.pstatus.sort'),
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
