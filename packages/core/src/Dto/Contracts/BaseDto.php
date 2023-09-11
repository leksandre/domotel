<?php

declare(strict_types=1);

namespace Kelnik\Core\Dto\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

abstract readonly class BaseDto implements Arrayable, JsonSerializable
{
    public function toArray(): array
    {
        $properties = array_keys(get_class_vars($this::class));
        $res = [];

        foreach ($properties as $propertyName) {
            $res[$propertyName] = $this->{$propertyName};
        }

        return $res;
    }

    public function jsonSerialize(): string
    {
        return json_encode($this->toArray());
    }
}
