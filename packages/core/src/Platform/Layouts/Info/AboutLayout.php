<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Info;

use Orchid\Icons\IconComponent;
use Orchid\Screen\Layouts\Legend;
use Orchid\Screen\Repository;
use Orchid\Screen\Sight;

final class AboutLayout extends Legend
{
    protected $target = 'info';

    public function build(Repository $repository)
    {
        $this->canSee($repository->get('isDeveloper', false));

        return parent::build($repository);
    }

    protected function columns(): iterable
    {
        return [
            Sight::make('laravel', 'Laravel'),
            Sight::make('orchid', 'Orchid Platform'),
            Sight::make('php', 'PHP')->render(
                fn(Repository $query) => $query->get('php') .
                    ' <a href="' . $this->query->get('phpLink', '') . '" target="_blank">' .
                    resolve(
                        IconComponent::class,
                        ['path' => 'bs.info-circle', 'class' => 'text-primary align-middle']
                    )->render()() .
                    '</a>'
            ),
            Sight::make('composer', 'Composer'),
            Sight::make('database', trans('kelnik-core::admin.about.database')),
            Sight::make('mailer', trans('kelnik-core::admin.about.mailer')),
            Sight::make('environment', trans('kelnik-core::admin.about.env')),
            Sight::make('debug', trans('kelnik-core::admin.about.debug.title'))->render(
                function (Repository $query) {
                    if ($query->get('debug') === false) {
                        return trans('kelnik-core::admin.about.debug.off', ['color' => 'text-success']);
                    }

                    $color = match ($query->get('environment')) {
                        'development', 'stage' => 'text-warning',
                        'production' => 'text-danger',
                        default => ''
                    };

                    return trans('kelnik-core::admin.about.debug.on', ['color' => $color]);
                }
            ),
            Sight::make('queue', trans('kelnik-core::admin.about.queue'))
        ];
    }
}
