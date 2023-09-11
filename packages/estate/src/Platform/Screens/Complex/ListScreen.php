<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Complex;

use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Platform\Layouts\Complex\FilterSelection;
use Kelnik\Estate\Platform\Layouts\Complex\ListLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = ComplexRepository::class;
    protected int $priorityDefault = Complex::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.complex.edit';

    public function query(): array
    {
        $this->name = trans('kelnik-estate::admin.menu.complexes');

        return [
            'list' => resolve($this->repository)->getAllBySelectionForAdmin(FilterSelection::class),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.complex.sort'),
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
            ListLayout::class
        ];
    }
}
