<?php

declare(strict_types=1);

namespace Kelnik\Estate\View\Components\PremisesCard\Layouts;

use Closure;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

final class MetaLayout extends Rows
{
    public function __construct(private readonly Fieldable|Groupable|Closure $tabFooter)
    {
    }

    protected function fields(): array
    {
        return [
            Input::make('data.meta.title')
                ->title('kelnik-estate::admin.components.premisesCard.meta.title_')
                ->maxlength(255),

            Input::make('data.meta.description')
                ->title('kelnik-estate::admin.components.premisesCard.meta.description')
                ->maxlength(255),

            Input::make('data.meta.keywords')
                ->title('kelnik-estate::admin.components.premisesCard.meta.keywords')
                ->maxlength(255)
                ->help('kelnik-estate::admin.components.premisesCard.meta.keywordsHelp'),

            (new class extends Field {
                protected $attributes = ['name' => 'replacement'];

                public function render(): string
                {
                    $html = '<div class="form-group"><h5>';
                    $html .= trans('kelnik-estate::admin.components.premisesCard.replacement.title');
                    $html .= '</h5><table class="table table-sm table-hover"><tr><th>';
                    $html .= trans('kelnik-estate::admin.components.premisesCard.replacement.src');
                    $html .= '</th><th>';
                    $html .= trans('kelnik-estate::admin.components.premisesCard.replacement.val');
                    $html .= '</th></tr>';

                    foreach ($this->get('value') as $fieldName => $field) {
                        $html .= '<tr><td>' . $field['var'] . '</td><td>' . $field['title'] . '</td></tr>';
                    }

                    $html .= '</table></div>';

                    return $html;
                }
            }),

            is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
        ];
    }
}
