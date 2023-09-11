<?php

declare(strict_types=1);

namespace Kelnik\Menu\Platform\Layouts\Menu;

use Kelnik\Menu\Models\Enums\Type;
use Kelnik\Menu\Models\Menu;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        /** @var ?Menu $menu */
        $menu = $this->query->get('menu');

        return [
            Input::make('menu.title')
                ->title('kelnik-menu::admin.title')
                ->maxlength(255)
                ->required(),
            Select::make('menu.type')
                ->title('kelnik-menu::admin.menuType')
                ->options($this->query->get('types', []))
                ->value($menu?->type->value)
                ->required()
                ->disabled($menu?->exists)
                ->addBeforeRender(function () {
                    $value = $this->get('value');
                    $this->set('value', $value instanceof Type ? $value->value : $value);
                }),
            Switcher::make('menu.active')->title('kelnik-menu::admin.active')->sendTrueOrFalse()
        ];
    }
}
