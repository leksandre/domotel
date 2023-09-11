<?php

declare(strict_types=1);

namespace Kelnik\Page\View\Components\Location\Layouts;

use Closure;
use Kelnik\Core\Platform\Fields\Picture;
use Kelnik\Page\Platform\Fields\MatrixMarkers;
use Kelnik\Page\Providers\PageServiceProvider;
use Orchid\Screen\Contracts\Fieldable;
use Orchid\Screen\Contracts\Groupable;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Blank;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

final class MapMarkersLayout extends Blank
{
    public const INDEPENDENT_MARKER_GROUP = '__no_group__';

    public function __construct(private Closure|Fieldable|Groupable $tabFooter)
    {
    }

    protected function layouts(): array
    {
        $types = $this->query
                    ? $this->query->get('data.map.markerTypes', [])
                    : [];
        $types = array_filter($types, static fn($el) => !empty($el['code']));

        $matrix = MatrixMarkers::make('markers.' . self::INDEPENDENT_MARKER_GROUP)
            ->title('kelnik-page::admin.components.location.marker.header')
            ->maxRows(50)
            ->columns([
                trans('kelnik-page::admin.components.location.marker.icon') => 'icon',
                trans('kelnik-page::admin.components.location.marker.coords') => 'coords',
                trans('kelnik-page::admin.components.location.marker.code') => 'code',
                trans('kelnik-page::admin.components.location.marker.title') => 'title',
                trans('kelnik-page::admin.components.location.marker.image') => 'image',
                trans('kelnik-page::admin.components.location.marker.description') => 'description',
            ])
            ->fields([
                'icon' => Picture::make()
                    ->targetId()
                    ->class('matrix_picture')
                    ->groups(PageServiceProvider::MODULE_NAME),
                'coords' => Input::make()->maxlength(80)->required(),
                'code' => Input::make()->maxlength(150),
                'title' => Input::make()->maxlength(80)->required(),
                'image' => Picture::make()
                    ->targetId()
                    ->class('matrix_picture')
                    ->groups(PageServiceProvider::MODULE_NAME),
                'description' => TextArea::make()->style('height: 80px !important; font-size: 12px'),
            ])
            ->help('kelnik-page::admin.components.location.marker.help');

        $list = [
            trans('kelnik-page::admin.components.location.marker.noGroup') => Layout::rows([$matrix])
        ];

        $matrixBlock = clone $matrix;
        $matrixBlock
            ->columns([
                trans('kelnik-page::admin.components.location.marker.coords') => 'coords',
                trans('kelnik-page::admin.components.location.marker.code') => 'code',
                trans('kelnik-page::admin.components.location.marker.title') => 'title',
                trans('kelnik-page::admin.components.location.marker.image') => 'image',
                trans('kelnik-page::admin.components.location.marker.description') => 'description',
            ])
            ->fields([
                'coords' => Input::make()->maxlength(80),
                'code' => Input::make()->maxlength(150),
                'title' => Input::make()->maxlength(80),
                'image' => Picture::make()
                    ->targetId()
                    ->class('matrix_picture')
                    ->groups(PageServiceProvider::MODULE_NAME),
                'description' => TextArea::make()->style('height: 80px !important; font-size: 12px'),
            ]);

        if ($types) {
            foreach ($types as $type) {
                $typeMatrix = clone $matrixBlock;
                $typeMatrix->name('markers.' . $type['code'])->markerType($type['code']);
                $list[$type['title']] = Layout::rows([$typeMatrix]);
            }
        }

        return [
            Layout::accordion($list),
            Layout::rows([
                is_callable($this->tabFooter) ? call_user_func($this->tabFooter) : $this->tabFooter
            ])
        ];
    }

    public function build(Repository $repository)
    {
        $data = $repository->get('data');
        $markers = self::rebuildMarkersList($data['map']['markers'] ?? []);

        $repository->set('markers', $markers);

        $this->query = $repository;
        $this->layouts = $this->layouts();

        return $this->buildAsDeep($repository);
    }

    private static function rebuildMarkersList(array $markers): array
    {
        if (!$markers) {
            return $markers;
        }

        $newMarkers = [];
        foreach ($markers as $marker) {
            $typename = $marker['type'] ?: self::INDEPENDENT_MARKER_GROUP;
            $newMarkers[$typename][] = $marker;
        }

        return $newMarkers;
    }
}
