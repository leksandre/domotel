<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Layouts\Premises;

use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Core\Platform\Fields\Upload;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Layouts\Rows;

final class EditImageLayout extends Rows
{
    protected function fields(): array
    {
        $picture = Picture::make('premises.image_list_id')
            ->title('kelnik-estate::admin.premises.imageList')
            ->storage(config('kelnik-estate.storage.disk'))
            ->groups(EstateServiceProvider::MODULE_NAME)
            ->targetId();

        return [
            $picture,
            (clone $picture)
                ->set('name', 'premises.image_plan_id')
                ->title('kelnik-estate::admin.premises.imagePlan'),
            (clone $picture)
                ->set('name', 'premises.image_plan_furniture_id')
                ->title('kelnik-estate::admin.premises.imagePlanFurniture'),
            (clone $picture)
                ->set('name', 'premises.image_3d_id')
                ->title('kelnik-estate::admin.premises.image3D'),
            (clone $picture)
                ->set('name', 'premises.image_on_floor_id')
                ->title('kelnik-estate::admin.premises.imageOnFloor'),
            Upload::make('premises.gallery')
                ->title('kelnik-estate::admin.premises.gallery')
                ->acceptedFiles('image/*')
                ->storage(config('kelnik-estate.storage.disk'))
                ->groups(EstateServiceProvider::MODULE_NAME),

            Button::make(trans('kelnik-estate::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveRow')
        ];
    }
}
