<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Section;

use Illuminate\Http\Request;
use Kelnik\Estate\Models\Section;
use Kelnik\Estate\Platform\Layouts\Section\FilterSelection;
use Kelnik\Estate\Platform\Layouts\Section\ListLayout;
use Kelnik\Estate\Platform\Layouts\Section\ListPaginatedLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\SectionRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = SectionRepository::class;
    protected int $priorityDefault = Section::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.section.edit';
    private bool $paginated = false;

    public function query(Request $request): array
    {
        $this->name = trans('kelnik-estate::admin.menu.sections');
        $this->paginated = $request->missing('building');
        $repository = resolve($this->repository);

        return [
            'list' => $this->paginated
                ? $repository->getAllBySelectionForAdminPaginated(FilterSelection::class)
                : $repository->getAllBySelectionForAdmin(FilterSelection::class),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.section.sort'),
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
