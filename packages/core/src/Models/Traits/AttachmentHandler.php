<?php

declare(strict_types=1);

namespace Kelnik\Core\Models\Traits;

use Kelnik\Core\Repositories\Contracts\AttachmentRepository;

trait AttachmentHandler
{
    protected static function bootAttachmentHandler(): void
    {
        static::updated(function (self $el) {
            $ids = [];

            foreach ($el->getAttachmentAttributes() as $fieldName) {
                if ($el->getOriginal($fieldName) && $el->isDirty($fieldName)) {
                    $ids[] = $el->getOriginal($fieldName);
                }
            }

            if ($ids) {
                resolve(AttachmentRepository::class)->deleteMass($ids);
            }
        });

        static::deleted(function (self $el) {
            if (!$el->getAttachmentAttributes()) {
                return;
            }

            $ids = [];

            foreach ($el->getAttachmentAttributes() as $fieldName) {
                $val = $el->getAttribute($fieldName);

                if ($val && is_numeric($val)) {
                    $ids[] = (int)$val;
                }
            }

            if ($ids) {
                resolve(AttachmentRepository::class)->deleteMass($ids);
            }
        });
    }

    public function getAttachmentAttributes(): array
    {
        return $this->attachmentAttributes;
    }
}
