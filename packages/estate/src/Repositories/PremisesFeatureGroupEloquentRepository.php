<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Kelnik\Estate\Models\Contracts\EstateModel;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\PremisesFeatureGroupRepository;

final class PremisesFeatureGroupEloquentRepository extends EstateEloquentRepository implements
    PremisesFeatureGroupRepository
{
    protected string $modelNamespace = PremisesFeatureGroup::class;

    public function findByPrimary(int|string $primary): PremisesFeatureGroup
    {
        return $this->modelNamespace::findOrNew($primary);
    }

    public function getAllWithFeatures(): Collection
    {
        return $this->modelNamespace::with([
            'features' => fn(HasMany $builder) => $builder
                ->select('id', 'group_id', 'title')
                ->orderBy('priority')
                ->orderBy('title')
        ])->adminList()->get();
    }

    /**
     * @param PremisesFeatureGroup $model
     * @param array|null $features
     *
     * @return bool
     */
    public function save(EstateModel $model, ?array $features = null): bool
    {
        $res = $model->save();

        if (!$res) {
            return $res;
        }

        if (!$features) {
            if (is_array($features)) {
                $model->features()->get()->each->delete();
            }

            return $res;
        }

        $newTypes = new Collection(array_values($features));

        $model->features->each(static function (PremisesFeature $el) use (&$newTypes) {
            $elIndex = 0;
            $elFromRequest = $newTypes->first(static function ($newElement, $key) use ($el, &$elIndex) {
                $elIndex = $key;
                return (int)($newElement['id'] ?? 0) === $el->getKey();
            });

            if (!$elFromRequest) {
                $el->delete();
                return;
            }

            $elFromRequest['priority'] = PremisesFeature::PRIORITY_DEFAULT + $elIndex;

            unset($elFromRequest['id']);
            $newTypes->forget($elIndex);

            $el->fill($elFromRequest)->save();
        });

        if ($newTypes) {
            foreach ($newTypes as $index => $el) {
                $el['priority'] = PremisesFeature::PRIORITY_DEFAULT + (int)$index;
                unset($el['id']);
                (new PremisesFeature($el))->featureGroup()->associate($model)->save();
            }
        }

        return $res;
    }

    public function getGeneral(): PremisesFeatureGroup
    {
        return $this->modelNamespace::where('general', true)->firstOrNew();
    }


    public function getGeneralKey(): int
    {
        return Cache::tags(EstateServiceProvider::MODULE_NAME)
            ->remember(
                $this->modelNamespace::CACHE_GENERAL_GROUP_ID,
                now()->addDay()->diffInRealSeconds(),
                fn() => $this->modelNamespace::query()
                    ->select(['id'])
                    ->firstOrCreate(
                        ['general' => true],
                        ['title' => trans('kelnik-estate::factory.premisesFeatureGroupVariants.general')]
                    )?->getKey() ?? 0
            );
    }
}
