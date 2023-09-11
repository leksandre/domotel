<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Layouts\Field;

use Kelnik\Form\Platform\Services\Contracts\FormPlatformService;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class ModalLayout extends Rows
{
    protected function fields(): array
    {
        $options = resolve(FormPlatformService::class)->getFieldTypes();
        natsort($options);

        return [
            Select::make('field')
                ->title('kelnik-form::admin.field')
                ->options($options)
        ];
    }
}
