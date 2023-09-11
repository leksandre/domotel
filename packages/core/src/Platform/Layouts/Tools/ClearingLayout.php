<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Tools;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Repository;
use Orchid\Support\Color;

final class ClearingLayout extends Rows
{
    public function __construct()
    {
        $this->title = trans('kelnik-core::admin.tools.clearing.title');
    }

    public function build(Repository $repository)
    {
        return parent::build($repository);
    }

    protected function fields(): iterable
    {
        return [
            Select::make('modules')
                ->title('kelnik-core::admin.tools.clearing.modules.title')
                ->multiple()
                ->options($this->query->get('modules', []))
                ->help('kelnik-core::admin.tools.clearing.modules.confirm')
                ->required(),
            Button::make(trans('kelnik-core::admin.tools.clearing.modules.button'))
                ->icon('bs.trash3')
                ->type(Color::DANGER)
                ->confirm(trans('kelnik-core::admin.tools.clearing.modules.confirm'))
                ->method('clearing')
        ];
    }
}
