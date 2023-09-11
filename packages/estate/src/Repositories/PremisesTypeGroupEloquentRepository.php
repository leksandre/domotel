<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;

final class PremisesTypeGroupEloquentRepository extends EstateEloquentRepository implements PremisesTypeGroupRepository
{
    protected string $modelNamespace = PremisesTypeGroup::class;

    public function findByPrimary(int|string $primary): PremisesTypeGroup
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function isUnique(PremisesTypeGroup $premisesTypeGroup): bool
    {
        $query = $this->modelNamespace::query()->where('slug', '=', $premisesTypeGroup->slug)->limit(1);

        if ($premisesTypeGroup->exists) {
            $query->whereKeyNot($premisesTypeGroup->id);
        }

        return $query->get('id')->count() === 0;
    }

    public function getAllWithTypes(): Collection
    {
        return $this->modelNamespace::with([
            'types' => fn(HasMany $builder) => $builder
                ->select('id', 'group_id', 'title')
                ->orderBy('priority')
                ->orderBy('title'),
            'image'
        ])->adminList()->get();
    }

    /**
     * @param PremisesTypeGroup $model
     * @param array|null $types
     *
     * @return bool
     */
    public function save(EstateModel $model, ?array $types = null): bool
    {
        $res = $model->save();

        if (!$res) {
            return $res;
        }

        if (!$types) {
            if (is_array($types)) {
                $model->types()->get()->each->delete();
            }

            return $res;
        }

        $newTypes = new Collection(array_values($types));

        $model->types->each(static function (PremisesType $el) use (&$newTypes) {
            $elIndex = 0;
            $elFromRequest = $newTypes->first(static function ($newElement, $key) use ($el, &$elIndex) {
                $elIndex = $key;
                return (int)($newElement['id'] ?? 0) === $el->getKey();
            });

            if (!$elFromRequest) {
                $el->delete();
                return;
            }

            $elFromRequest['priority'] = PremisesType::PRIORITY_DEFAULT + $elIndex;

            unset($elFromRequest['id']);
            $newTypes->forget($elIndex);

            $el->fill($elFromRequest)->save();
        });

        if ($newTypes) {
            foreach ($newTypes as $index => $el) {
                $el['priority'] = PremisesType::PRIORITY_DEFAULT + (int)$index;
                unset($el['id']);
                (new PremisesType($el))->typeGroup()->associate($model)->save();
            }
        }

        return $res;
    }
}
