<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Settings;

use Kelnik\Core\Platform\Fields\ColorPicker;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Layouts\Rows;

final class ColorsLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Group::make([
                ColorPicker::make('colors.brand-base')
                    ->title('kelnik-core::admin.settings.colors.brand-base')
                    ->label('$brand-base'),
                ColorPicker::make('colors.brand-light')
                    ->title('kelnik-core::admin.settings.colors.brand-light')
                    ->label('$brand-light'),
                ColorPicker::make('colors.brand-dark')
                    ->title('kelnik-core::admin.settings.colors.brand-dark')
                    ->label('$brand-dark')
            ])->autoWidth(),
            Group::make([
                ColorPicker::make('colors.brand-text')
                    ->title('kelnik-core::admin.settings.colors.brand-text')
                    ->label('$brand-text'),
                ColorPicker::make('colors.brand-gray')
                    ->title('kelnik-core::admin.settings.colors.brand-gray')
                    ->label('$brand-gray'),
                ColorPicker::make('colors.brand-headers')
                    ->title('kelnik-core::admin.settings.colors.brand-headers')
                    ->label('$brand-headers'),
            ])->autoWidth(),
            Group::make([
                ColorPicker::make('colors.additional-1')
                    ->title('kelnik-core::admin.settings.colors.additional-1')
                    ->label('$additional-1'),
                ColorPicker::make('colors.additional-2')
                    ->title('kelnik-core::admin.settings.colors.additional-2')
                    ->label('$additional-2'),
                ColorPicker::make('colors.additional-3')
                    ->title('kelnik-core::admin.settings.colors.additional-3')
                    ->label('$additional-3'),
                ColorPicker::make('colors.additional-4')
                    ->title('kelnik-core::admin.settings.colors.additional-4')
                    ->label('$additional-4'),
                ColorPicker::make('colors.additional-5')
                    ->title('kelnik-core::admin.settings.colors.additional-5')
                    ->label('$additional-5')
            ])->autoWidth(),
            Button::make(trans('kelnik-core::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSettings')
        ];
    }
}
