<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\Sources\Xml;

use Illuminate\Support\MessageBag;
use Kelnik\EstateImport\PreProcessor\Contracts;

final class Filter implements Contracts\Filter
{
    private const EMPTY_UID = '00000000-0000-0000-0000-000000000000';

    public function __invoke(string $modelName, array $data): bool|MessageBag
    {
        if (isset($data['external_id']) && $data['external_id'] === self::EMPTY_UID) {
            return new MessageBag(['']);
        }

        return true;
    }
}
