<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Layouts\History;

use Kelnik\EstateImport\Platform\Components\StateInfo;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

final class ListLayout extends Table
{
    protected $target = 'history';

//    public function __construct()
//    {
//        $this->title = trans('kelnik-estate-import::admin.history.title');
//    }

    protected function columns(): array
    {
        return [
            TD::make('id', 'ID'),
            TD::make('created_at', trans('kelnik-estate-import::admin.created'))->dateTimeString(),
            TD::make('state', trans('kelnik-estate-import::admin.history.state.title'))
                ->component(StateInfo::class)
                ->width('auto')
        ];
    }
}
