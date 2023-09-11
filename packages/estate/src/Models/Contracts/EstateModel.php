<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Contracts;

use Illuminate\Database\Eloquent\Model;
use Kelnik\Core\Models\Traits\AttachmentHandler;

abstract class EstateModel extends Model
{
    use AttachmentHandler;

    public const PRIORITY_DEFAULT = 500;
    public const EXTERNAL_ID_MAX_LENGTH = 255;

    /** @var string[] */
    protected array $attachmentAttributes = [];
}
