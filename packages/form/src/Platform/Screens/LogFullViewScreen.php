<?php

declare(strict_types=1);

namespace Kelnik\Form\Platform\Screens;

use Kelnik\Form\Models\Form;
use Kelnik\Form\Models\Log;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Symfony\Component\HttpFoundation\Response;

final class LogFullViewScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;
    private ?Form $form = null;
    private ?Log $log = null;

    public function query(Log $log): array
    {
        abort_if(!$log->exists, Response::HTTP_NOT_FOUND);

        $this->log = $log;
        $this->form = $this->log->form;
        $this->name = trans('kelnik-form::admin.menu.log') . ': ' . $this->form?->title;
        $this->exists = $log->exists;

        return [
            'log' => $log
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-form::admin.backToLogList'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('form.logs.list')),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('log.data.fields', [
                TD::make('title', 'Title')->render(fn($row) => $row['title']),
                TD::make('value', 'Value')->render(fn($row) => $row['value']),
            ]),
            Layout::legend('log', [
                Sight::make('created_at', trans('kelnik-form::admin.created'))->dateTimeString(),
                Sight::make('data.sourceUrl', trans('kelnik-form::admin.referer')),
                Sight::make('data.client.ip', 'IP')->render(static function (Log $log) {
                    return implode(', ', $log->data['client']['ip']);
                }),
                Sight::make('data.client.browser', trans('kelnik-form::admin.browser')),
            ])
        ];
    }
}
