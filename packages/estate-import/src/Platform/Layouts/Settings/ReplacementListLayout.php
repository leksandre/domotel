<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Platform\Layouts\Settings;

use Kelnik\Core\Platform\Fields\Matrix;
use Kelnik\EstateImport\Sources\Contracts\SourceType;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

final class ReplacementListLayout extends Rows
{
    public function __construct()
    {
        $this->title = trans('kelnik-estate-import::admin.history.replacement.list');
    }

    protected function fields(): array
    {
        /** @var SourceType $source */
        $source = $this->query->get('source');

        return [
            Matrix::make('settings.' . $source->getName() . '.replacement.list')
                ->maxRows(50)
                ->columns([
                    trans('kelnik-estate-import::admin.history.replacement.src') => 'src',
                    trans('kelnik-estate-import::admin.history.replacement.dst') => 'dst',
                ])
                ->fields([
                    'src' => TextArea::make()->style('height: 150px !important'),
                    'dst' => Input::make()
                ])
                ->value($this->query->get('params.replacement.list'))
                ->help('kelnik-estate-import::admin.history.replacement.help')
        ];
    }
}
