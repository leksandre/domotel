<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Layouts\Settings;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class MainLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Select::make('settings.source')
                ->title('kelnik-estate-import::admin.menu.source')
                ->options($this->query->get('sources', []))
                ->value($this->query->get('source')?->getName())
                ->required(),
            Button::make(trans('kelnik-estate-import::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSettings')
        ];
    }
}
