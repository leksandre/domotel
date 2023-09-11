<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\Completion;

use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Platform\Layouts\Completion\ListLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\CompletionRepository;

final class ListScreen extends BaseScreen
{
    protected string $repository = CompletionRepository::class;
    protected int $priorityDefault = Completion::PRIORITY_DEFAULT;
    protected string $routeToEdit = 'estate.completion.edit';

    public function query(): array
    {
        $this->name = trans('kelnik-estate::admin.menu.completions');

        return [
            'list' => resolve($this->repository)->getAllForAdmin(),
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('estate.completion.sort'),
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
