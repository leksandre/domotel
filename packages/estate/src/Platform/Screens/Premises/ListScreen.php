<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Premises;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Platform\Layouts\Premises\FilterSelection;
use Kelnik\Estate\Platform\Layouts\Premises\ListLayout;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

final class ListScreen extends Screen
{
    private string $repository = PremisesRepository::class;
    private string $routeToEdit = 'estate.premises.edit';
    private CoreService $coreService;

    protected ?string $name = null;

    public function __construct()
    {
        $this->coreService = resolve(CoreService::class);
    }

    public function query(): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premises');

        return [
            'list' => resolve($this->repository)->getAllBySelectionForAdminPaginated(FilterSelection::class),
            'coreService' => $this->coreService
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate::admin.add'))
                ->icon('bs.plus-circle')
                ->route($this->coreService->getFullRouteName($this->routeToEdit))
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
