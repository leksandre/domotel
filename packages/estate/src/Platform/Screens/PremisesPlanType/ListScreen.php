<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesPlanType;

use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Platform\Layouts\PremisesPlanType\ListLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\PremisesPlanTypeRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = PremisesPlanTypeRepository::class;
    protected int $priorityDefault = PremisesPlanType::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.pplantype.edit';

    public function query(): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesPlanTypes');

        return [
            'list' => resolve($this->repository)->getAllForAdmin(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.pplantype.sort'),
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
