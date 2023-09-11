<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\PremisesStatus;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        return [
            Input::make('status.title')
                ->title('kelnik-estate::admin.title')
                ->required(),
            Select::make('status.replace_id')
                ->title('kelnik-estate::admin.premisesStatus.replace')
                ->options(resolve(PremisesStatusRepository::class)->getAll()->pluck('title', 'id')->toArray())
                ->empty(trans('kelnik-estate::admin.premisesStatus.empty'), '0'),
            Switcher::make('status.premises_card_available')
                ->title('kelnik-estate::admin.premisesStatus.premisesCardAvailable')
                ->sendTrueOrFalse(),
            Switcher::make('status.hide_price')
                ->title('kelnik-estate::admin.premisesStatus.hidePrice')
                ->sendTrueOrFalse(),
            Switcher::make('status.take_stat')
                ->title('kelnik-estate::admin.premisesStatus.takeStat')
                ->sendTrueOrFalse(),
            Input::make('status.additional_text')
                ->title('kelnik-estate::admin.premisesStatus.additionalText')
                ->maxlength(255),
            Picture::make('status.icon_id')
                ->title('kelnik-estate::admin.premisesStatus.icon')
                ->storage(config('kelnik-estate.storage.disk'))
                ->groups(EstateServiceProvider::MODULE_NAME)
                ->targetId(),
            Input::make('status.external_id')
                ->title('kelnik-estate::admin.external_id')
                ->maxlength(255),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
