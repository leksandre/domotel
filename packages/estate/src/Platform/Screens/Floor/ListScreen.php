<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Floor;

use Illuminate\Http\Request;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Platform\Layouts\Floor\FilterSelection;
use Kelnik\Estate\Platform\Layouts\Floor\ListLayout;
use Kelnik\Estate\Platform\Layouts\Floor\ListPaginatedLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\FloorRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = FloorRepository::class;
    protected int $priorityDefault = Floor::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.floor.edit';
    private bool $paginated = false;

    public function query(Request $request): array
    {
        $this->name = trans('kelnik-estate::admin.menu.floors');
        $this->paginated = $request->missing('building');
        $repository = resolve($this->repository);

        return [
            'list' => $this->paginated
                ? $repository->getAllBySelectionForAdminPaginated(FilterSelection::class)
                : $repository->getAllBySelectionForAdmin(FilterSelection::class),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.floor.sort'),
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
