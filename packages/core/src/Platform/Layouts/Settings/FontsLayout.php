<?php

declare(strict_types=1);

namespace Kelnik\Core\Platform\Layouts\Settings;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Builder;
use Orchid\Screen\Layouts\Rows;
use Orchid\Screen\Repository;

final class FontsLayout extends Rows
{
    protected $template = 'kelnik-core::platform.layouts.settingsFonts';

    protected function fields(): array
    {
        return [
            Button::make(trans('kelnik-core::admin.save'))
                ->icon('bs.save')
                ->class('btn btn-secondary')
                ->method('saveSettings')
        ];
    }

    public function build(Repository $repository)
    {
        $this->query = $repository;

        if (!$this->isSee()) {
            return;
        }

        $form = new Builder($this->fields(), $repository);

        $accept = array_map(static fn($el) => '.' . $el, config('kelnik-core.theme.fonts.ext'));
        $accept = implode(',', $accept);

        return view($this->template, [
            'fonts'  => $this->query->get('fonts'),
            'form' => $form->generateForm(),
            'title' => $this->title,
            'accept' => $accept,
        ]);
    }
}
