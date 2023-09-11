<?php

declare(strict_types=1);

namespace Kelnik\Estate\Repositories\Traits;

use Illuminate\Support\Collection;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Estate\Models\PremisesFeature;

trait LoadAttachments
{
    private function loadAttachments(array|Collection $collection, array $fieldToRelation = []): array|Collection
    {
        $hasData = $collection instanceof Collection
            ? $collection->isNotEmpty()
            : $collection;

        if (!$hasData || !$fieldToRelation) {
            return $collection;
        }

        $attachIds = [];

        foreach ($collection as &$el) {
            foreach ($fieldToRelation as $fieldName => $relationName) {
                $imgId = $el->getAttribute($fieldName);
                if ($imgId) {
                    $attachIds[$imgId] = $imgId;
                }
            }
            if ($el->relationLoaded('floor') && $el->floor->relationLoaded('building')) {
                $imgId = $el->floor->building->getAttribute('complex_plan_image_id');
                if ($imgId) {
                    $attachIds[$imgId] = $imgId;
                }
            }
            if ($el->relationLoaded('features')) {
                $el->features->each(function (PremisesFeature $pf) use (&$attachIds) {
                    if ($pf->icon_id) {
                        $attachIds[$pf->icon_id] = $pf->icon_id;
                    }
                });
            }
        }

        if (!$attachIds) {
            return $collection;
        }

        /** @var Collection $attachments */
        $attachments = resolve(AttachmentRepository::class)->getByPrimary($attachIds)->pluck(null, 'id');
        unset($attachIds);

        // Set attachments
        //
        foreach ($collection as &$el) {
            foreach ($fieldToRelation as $fieldName => $relationName) {
                if ($el->getAttribute($fieldName) || $attachments->has($el->getAttribute($fieldName))) {
                    $el->setRelation($relationName, $attachments->get($el->getAttribute($fieldName)));
                }
                if ($el->relationLoaded('floor') && $el->floor->relationLoaded('building')) {
                    $imgId = $el->floor->building->getAttribute('complex_plan_image_id');

                    if ($imgId && $attachments->has($imgId)) {
                        $el->floor->building->setRelation('complexPlan', $attachments->get($imgId));
                    }
                }
                if ($el->relationLoaded('features')) {
                    $el->features->each(function (PremisesFeature $pf) use ($attachments) {
                        if ($pf->icon_id && $attachments->has($pf->icon_id)) {
                            $pf->setRelation('icon', $attachments->get($pf->icon_id));
                        }
                    });
                }
            }
        }

        return $collection;
    }
}
