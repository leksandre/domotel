<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Contracts;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Core\Models\Traits\AttachmentHandler;

abstract class EstateVisualModel extends Model
{
    use AttachmentHandler;

    /** @var string[] */
    protected array $attachmentAttributes = [];
}
