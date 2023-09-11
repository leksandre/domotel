<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Completion;

use Kelnik\Estate\Platform\Layouts\BaseListLayout;

final class ListLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.completion.edit';
    protected string $routeToList = 'estate.completion.list';
}
