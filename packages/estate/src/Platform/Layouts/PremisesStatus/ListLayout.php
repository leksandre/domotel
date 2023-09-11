<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesStatus;

use Kelnik\Estate\Platform\Layouts\BaseListLayout;

final class ListLayout extends BaseListLayout
{
    protected string $routeToEdit = 'estate.pstatus.edit';
    protected string $routeToList = 'estate.pstatus.list';
}
