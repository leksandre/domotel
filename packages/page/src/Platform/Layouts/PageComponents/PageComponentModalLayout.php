<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Layouts\PageComponents;

use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

final class PageComponentModalLayout extends Rows
{
    protected function fields(): array
    {
        /** @var BladeComponentRepository $service */
        $service = resolve(BladeComponentRepository::class);

        return [
            Select::make('component_id')
                ->title('kelnik-page::admin.component')
                ->options($service->getAdminList())
        ];
    }
}
