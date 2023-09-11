<?php

declare(strict_types=1);

namespace Kelnik\Estate\Database\Seeders;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kelnik\Estate\Jobs\StatUpdate;
use Kelnik\Estate\Models\Building;
use Kelnik\Estate\Models\Completion;
use Kelnik\Estate\Models\Complex;
use Kelnik\Estate\Models\Floor;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Models\PremisesFeature;
use Kelnik\Estate\Models\PremisesFeatureGroup;
use Kelnik\Estate\Models\PremisesFeatureReference;
use Kelnik\Estate\Models\PremisesStatus;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Models\Section;

final class EstateSeeder extends BaseSeeder
{
    private const MIN_COMPLEXES = self::MAX_COMPLEXES;
    private const MAX_COMPLEXES = 1;
    private const MIN_BUILDINGS_PER_COMPLEX = 1;
    private const MAX_BUILDINGS_PER_COMPLEX = 5;
    private const MAX_SECTIONS_PER_BUILDING = 4;
    private const MAX_FLOORS_PER_BUILDING = 10;
    private const MAX_FLATS_ON_FLOOR = 10;
    private const MAX_COMMERCE_ON_FLOOR = 6;
    private const MAX_PARKING_ON_FLOOR = 20;
    private const TYPE_LIVING = 'living';
    private const TYPE_COMMERCE = 'commerce';
    private const TYPE_PARKING = 'parking';
    private const GENERAL_FEATURES = 'general';

    private const TYPE_COMMERCE_FLOOR = 1;
    private const TYPE_LIVING_MIN_FLOOR = 2;
    private const TYPE_PARKING_MIN_FLOOR = -2;
    private const TYPE_PARKING_MAX_FLOOR = -1;
    private const TYPE_PARKING_BUILDING_MIN_FLOOR = 1;
    private const TYPE_PARKING_BUILDING_MAX_FLOOR = 8;

    public function run(): void
    {
        $this->truncateModels();

        $completions = Completion::factory()->count(rand(2, 5))->createQuietly();
        $buildings = [];
        $complexes = Complex::factory()
            ->count(rand(self::MIN_COMPLEXES, self::MAX_COMPLEXES))
            ->createQuietly(['active' => true]);

        $complexes->each(static function (Complex $complex) use (&$buildings, $completions) {
            $rows = Building::factory()
                ->count(rand(self::MIN_BUILDINGS_PER_COMPLEX, self::MAX_BUILDINGS_PER_COMPLEX))
                ->sequence(fn ($sequence) => [
                    'priority' => Building::PRIORITY_DEFAULT + $sequence->index,
                    'title' => $sequence->index + 1,
                    'slug' => $sequence->index + 1,
                    'completion_id' => $completions->random(1)?->first()?->getKey()
                ])
                ->make(['complex_id' => $complex->getKey(), 'active' => true])
                ->toArray();

            $buildings = array_merge($buildings, $rows);
        });
        unset($complexes);

        Building::query()->insert($buildings);
        $buildings = Building::query()->select(['id', 'complex_id', 'title'])->get();

        $floors = [];
        $sections = [];

        $buildings->each(static function (Building $building) use (&$floors, &$sections) {
            $rows = Floor::factory()
                ->count(rand(
                    abs(self::TYPE_PARKING_MIN_FLOOR) * 2,
                    self::MAX_FLOORS_PER_BUILDING + abs(self::TYPE_PARKING_MIN_FLOOR)
                ))
                ->sequence(function ($sequence) {
                    $number = $sequence->index + self::TYPE_PARKING_MIN_FLOOR;

                    if ($number >= 0) {
                        $number++;
                    }

                    return [
                        'priority' => Floor::PRIORITY_DEFAULT + $number,
                        'title' => $number,
                        'number' => $number,
                        'slug' => $number
                    ];
                })
                ->make([
                    'building_id' => $building->getKey(),
                    'active' => true
                ])
                ->toArray();

            $floors = array_merge($floors, $rows);

            $rows = Section::factory()
                ->count(rand(0, self::MAX_SECTIONS_PER_BUILDING))
                ->sequence(function ($sequence) {
                    $number = $sequence->index + 1;

                    return [
                        'priority' => Section::PRIORITY_DEFAULT + $number,
                        'title' => $number,
                        'slug' => $number
                    ];
                })
                ->make([
                    'building_id' => $building->getKey(),
                    'active' => true
                ])
                ->toArray();

            $sections = array_merge($sections, $rows);
        });

        Floor::query()->insert($floors);
        Section::query()->insert($sections);

        $floors = Floor::query()->select(['id', 'title', 'number', 'building_id'])->get();
        $sections = Section::query()->select(['id', 'title', 'building_id'])->get();

        // Statuses
        $variants = trans('kelnik-estate::factory.premisesStatusVariants');
        $statuses = new Collection();
        $i = 0;
        foreach ($variants as $el) {
            $statuses->add(
                PremisesStatus::factory()->createQuietly([
                    'priority' => PremisesStatus::PRIORITY_DEFAULT + $i++,
                    'premises_card_available' => $el['card_available'],
                    'title' => $el['title']
                ])
            );
        }

        // Types
        $groupVariants = trans('kelnik-estate::factory.premisesTypeGroupVariants');
        $variants = trans('kelnik-estate::factory.premisesTypeVariants');
        $shortNames = trans('kelnik-estate::factory.premisesTypeVariantsShort');
        $plurals = trans('kelnik-estate::factory.premisesTypeGroupPlurals');
        $types = new Collection();
        $i = 0;

        foreach ($groupVariants as $k => $v) {
            if (!isset($variants[$k])) {
                continue;
            }
            $living = $k === self::TYPE_LIVING;
            $group = PremisesTypeGroup::factory()->createQuietly([
                'priority' => PremisesTypeGroup::PRIORITY_DEFAULT + $i++,
                'living' => $living,
                'build_title' => $living && rand(0, 1),
                'title' => $v,
                'slug' => Str::slug($v),
                'plural' => $plurals[$k] ?? []
            ]);

            $elements = new Collection();
            foreach ($variants[$k] as $elK => $elV) {
                $elements->add(
                    PremisesType::factory()->createQuietly([
                        'group_id' => $group->getKey(),
                        'rooms' => $elK,
                        'priority' => PremisesType::PRIORITY_DEFAULT + $elK,
                        'title' => $elV,
                        'short_title' => $shortNames[$k][$elK] ?? null,
                        'slug' => Str::slug($elV)
                    ])
                );
            }

            $group->setRelation('types', $elements);
            $types->put($k, $group);
        }

        // Features
        $groupVariants = trans('kelnik-estate::factory.premisesFeatureGroupVariants');
        $variants = trans('kelnik-estate::factory.premisesFeatureVariants');
        $features = new Collection();
        $i = 0;

        foreach ($groupVariants as $k => $v) {
            if (!isset($variants[$k])) {
                continue;
            }
            $general = $k === self::GENERAL_FEATURES;
            $group = PremisesFeatureGroup::factory()->createQuietly([
                'active' => true,
                'priority' => PremisesFeatureGroup::PRIORITY_DEFAULT + $i++,
                'general' => $general,
                'title' => $v
            ]);

            $elements = new Collection();
            foreach ($variants[$k] as $elK => $elV) {
                $elements->add(
                    PremisesFeature::factory()->createQuietly([
                       'group_id' => $group->getKey(),
                       'active' => true,
                       'priority' => PremisesFeature::PRIORITY_DEFAULT + $elK,
                       'title' => $elV
                    ])
                );
            }

            $group->setRelation('features', $elements);
            $features->put($k, $group);
        }

        // Premises
        /**
         * @var Floor $floor
         * @var PremisesTypeGroup $types
         * @var PremisesType $type
         * @var PremisesStatus $status
         * @var Premises $flat
         */
        $complexLastPremisesNumber = $buildings->pluck('complex_id', 'id')->flip()->map(fn() => 0);
        $generalFeatures = $features->get(self::GENERAL_FEATURES)?->features ?? new Collection();
        $featureMax = $generalFeatures->count() - 1;

        if ($featureMax < 0) {
            $featureMax = 0;
        }

        $rows = [];

        foreach ($floors as $floor) {
            $section = $sections->first(fn(Section $section) => $section->building_id === $floor->building_id);
            $complexId = $buildings->first(fn(Building $building) => $building->getKey() === $floor->building_id)
                ?->complex_id;

            $cnt = self::MAX_FLATS_ON_FLOOR;
            $typeGroup = self::TYPE_LIVING;
            $isCommerce = $isParking = false;

            if ($floor->number === self::TYPE_COMMERCE_FLOOR) {
                $typeGroup = self::TYPE_COMMERCE;
                $cnt = self::MAX_COMMERCE_ON_FLOOR;
                $isCommerce = true;
            } elseif ($floor->number <= self::TYPE_PARKING_MAX_FLOOR) {
                $typeGroup = self::TYPE_PARKING;
                $cnt = self::MAX_PARKING_ON_FLOOR;
                $isParking = true;
            }

            $isLiving = $typeGroup === self::TYPE_LIVING;

            $premisesNumber = $complexLastPremisesNumber->get($typeGroup . '-' . $complexId) ?? 0;
            $premisesNumber++;
            $complexLastPremisesNumber->put($typeGroup . '-' . $complexId, $premisesNumber);

            for ($i = 1; $i <= $cnt; $i++) {
                $status = $statuses->random(1)->first();
                $type = $types->get($typeGroup)?->types->random(1)->first();

                if ($isCommerce) {
                    $area = $this->getCommerceArea();
                    $price = $this->getCommercePrice($area);
                } elseif ($isParking) {
                    $area = $this->getParkingArea($type);
                    $price = $this->getParkingPrice($area);
                } else {
                    $area = $this->getFlatArea($type);
                    $price = $this->getFlatPrice($type);
                }

                $priceMeter = $price / $area;
                $priceSale = 0;

                if ($priceMeter < 0) {
                    $priceMeter = 0;
                }

                if (rand(0, 1)) {
                    $priceSale = $price - ceil($price * (rand(1, 5) / 100));
                }

                $attributes = [
                    'active' => true,
                    'type_id' => $type->getKey(),
                    'original_type_id' => $type->getKey(),
                    'status_id' => $status->getKey(),
                    'original_status_id' => $status->getKey(),
                    'floor_id' => $floor->getKey(),
                    'section_id' => $section?->getKey(),
                    'price' => $price,
                    'price_total' => $price,
                    'price_sale' => $priceSale,
                    'price_meter' => $priceMeter,
                    'area_total' => $area,
                    'number' => $i,
                    'number_on_floor' => $i,
                    'title' => $premisesNumber
                ];

                if ($isLiving) {
                    $attributes['rooms'] = $type->rooms;
                    $attributes['area_living'] = $area * 0.95;
                    $attributes['area_kitchen'] = $this->getFlatAreaKitchen($type);
                }

                $rows[] = Premises::factory()->make($attributes)->toArray();
            }
        }

        Premises::query()->insert($rows);
        $premises = Premises::query()->select(['id', 'type_id'])->get();
        $rows = [];
        $livingTypes = $types
            ->first(static fn(PremisesTypeGroup $el) => $el->living)
            ?->types
            ->pluck('id')
            ->toArray() ?? [];

        foreach ($premises as $el) {
            if (!in_array($el->type_id, $livingTypes)) {
                continue;
            }

            $flatFeatures = $generalFeatures->random(rand(0, $featureMax));

            if ($flatFeatures->isNotEmpty()) {
                foreach ($flatFeatures as $feature) {
                    $rows[] = [
                        'premises_id' => $el->getKey(),
                        'feature_id' => $feature->getKey(),
                        'created_at' => now()->getTimestamp(),
                        'updated_at' => now()->getTimestamp()
                    ];
                }
            }
        }

        if ($rows) {
            PremisesFeatureReference::query()->insert($rows);
        }
        unset($rows);

        StatUpdate::dispatchSync();
    }

    public function truncateModels(): void
    {
        $models = [
            Completion::class,
            Complex::class,
            Building::class,
            Floor::class,
            Section::class,
            PremisesStatus::class,
            PremisesTypeGroup::class,
            PremisesType::class,
            PremisesFeatureGroup::class,
            PremisesFeature::class,
            PremisesFeatureReference::class,
            Premises::class
        ];

        foreach ($models as $modelNamespace) {
            $modelNamespace::query()->truncate();
        }
    }
}
