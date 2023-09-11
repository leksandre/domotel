<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Header\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Title;
use Kelnik\Page\View\Components\Header\Enums\Style;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Radio;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Layouts\Rows;

final class SettingsLayout extends Rows
{
    public function __construct(private Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        $styleValue = $this->query->get('data.content.style');

        $setValue = function ($field, $value) use ($styleValue) {
            $field->set('value', $value);
            $field->set('checked', $value === $styleValue);
        };

        $fields = [
            Switcher::make('active')
                ->title('kelnik-page::admin.active')
                ->sendTrueOrFalse(),
            Title::make('')->value(trans('kelnik-page::admin.components.header.style.title'))
        ];

        $i = 1;
        $cnt = count(Style::cases());

        foreach (Style::cases() as $case) {
            $field = Radio::make('data.content.style')
                ->placeholder($case->title())
                ->addBeforeRender(fn() => $setValue($this, $case->value));

            if ($i < $cnt) {
                $field->clear();
            }

            $fields[] = $field;
            $i++;
        }

        $fields[] = is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter;

        return $fields;
    }
}
