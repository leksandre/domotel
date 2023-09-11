<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Building;

use Illuminate\Http\Request;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Platform\Layouts\Building\FilterSelection;
use Kelnik\Estate\Platform\Layouts\Building\ListLayout;
use Kelnik\Estate\Platform\Layouts\Building\ListPaginatedLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\BuildingRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = BuildingRepository::class;
    protected int $priorityDefault = Building::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.building.edit';
    private bool $paginated = false;

    public function query(Request $request): array
    {
        $this->name = trans('kelnik-estate::admin.menu.buildings');
        $this->paginated = $request->missing('complex');
        $repository = resolve($this->repository);

        return [
            'list' => $this->paginated
                ? $repository->getAllBySelectionForAdminPaginated(FilterSelection::class)
                : $repository->getAllBySelectionForAdmin(FilterSelection::class),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.building.sort'),
                [],
                false
            ),
            'coreService' => $this->coreService
        ];
    }

    public function layout(): array
    {
        return [
            FilterSelection::class,
            $this->paginated
                ? ListPaginatedLayout::class
                : ListLayout::class
        ];
    }
}
