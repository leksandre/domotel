<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Filters;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\Section;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PremisesFilter extends BaseFilter
{
    public $parameters = [
        'id',
        'title',
        'floor',
        'section',
        'type',
        'status'
    ];

    public function run(Builder $builder): Builder
    {
        $id = $this->request->integer('id');
        $title = $this->request->get('title', '');

        if ($id > 0) {
            $builder->where('id', '=', $id);
        }

        if (strlen($title)) {
            $title = '%' . $title . '%';
            $builder->where(
                fn(Builder $builder) => $builder->where('title', 'like', $title)->orWhere('external_id', 'like', $title)
            );
        }

        $fields = [
            'floor' => 'floor_id',
            'section' => 'section_id',
            'type' => 'type_id',
            'status' => 'status_id'
        ];

        foreach ($fields as $k => $target) {
            $value = (array)$this->request->get($k);
            $value = array_map('intval', $value);
            $value = array_filter($value);

            if ($value) {
                $builder->whereIn($target, $value);
            }
        }

        return $builder;
    }

    /**
     * @return Field[]
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function display(): array
    {
        $type = new PremisesType();

        return [
            Input::make('id')
                ->placeholder('kelnik-estate::admin.filter.fieldId')
                ->value($this->request->get('id')),
            Input::make('title')
                ->placeholder('kelnik-estate::admin.filter.fieldTitleOrExternalId')
                ->value($this->request->get('title')),
            Relation::make('floor')
                ->fromModel(Floor::class, 'title')
                ->placeholder('kelnik-estate::admin.filter.fieldFloor')
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->multiple()
                ->allowEmpty()
                ->value($this->request->get('floor')),
            Relation::make('section')
                ->fromModel(Section::class, 'title')
                ->placeholder('kelnik-estate::admin.filter.fieldSection')
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->multiple()
                ->allowEmpty()
                ->value($this->request->get('section')),
            Relation::make('type')
                ->fromModel($type::class, $type->getTable() . '.title')
                ->placeholder('kelnik-estate::admin.filter.fieldType')
                ->applyScope('adminList')
                ->displayAppend('admin_title')
                ->multiple()
                ->allowEmpty()
                ->value($this->request->get('type')),
            Relation::make('status')
                ->fromModel(PremisesStatus::class, 'title')
                ->placeholder('kelnik-estate::admin.filter.fieldStatus')
                ->applyScope('adminList')
                ->multiple()
                ->allowEmpty()
                ->value($this->request->get('status'))
        ];
    }
}
