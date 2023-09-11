<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Site;

use Kelnik\Core\Models\Enums\Lang;
use Kelnik\Core\Models\Enums\Type;
use Kelnik\Core\Models\Site;
use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\Core\Platform\Services\Contracts\SitePlatformService;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

final class EditLayout extends Rows
{
    protected function fields(): array
    {
        $getEnumValue = fn (string|object $val): string
        => ($val instanceof Type || $val instanceof Lang ? $val->value : $val);

        /** @var ?Site $site */
        $site = $this->query->get('site');

        return [
            Input::make('site.title')
                ->title('kelnik-core::admin.site.title')
                ->required(),
            Switcher::make('site.active')->title('kelnik-core::admin.site.active')->sendTrueOrFalse(),
            Switcher::make('site.primary')
                ->title('kelnik-core::admin.site.primary.title')
                ->help('kelnik-core::admin.site.primary.help')
                ->sendTrueOrFalse(),
            Select::make('site.locale')
                ->title('kelnik-core::admin.site.locale')
                ->options($this->query->get('langs'))
                ->required()
                ->addBeforeRender(function () use ($getEnumValue) {
                    $this->set('value', $getEnumValue($this->get('value') ?? ''));
                }),
            Select::make('site.type')
                ->title('kelnik-core::admin.site.type')
                ->options($this->query->get('types'))
                ->required()
                ->addBeforeRender(function () use ($getEnumValue) {
                    $this->set('value', $getEnumValue($this->get('value') ?? ''));
                }),

            Matrix::make('site.hosts')
                ->title('kelnik-core::admin.site.hosts')
                ->fields([
                    Input::make('value')
                ])
                ->columns([
                    trans('kelnik-core::admin.site.host') => 'value'
                ])
                ->maxRows(SitePlatformService::HOSTS_MAX_COUNT),

            TextArea::make('site.settings.seo.robots')
                ->title('kelnik-core::admin.site.seo.robots')
                ->rows(5)
                ->value($site->settings->getSeoRobots()),

            Button::make(trans('kelnik-core::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSite')
        ];
    }
}
