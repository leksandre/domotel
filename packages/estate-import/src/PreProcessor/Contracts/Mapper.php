<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\PreProcessor\Contracts;

use Kelnik\EstateImport\Models\Proxy\Premises;

abstract class Mapper
{
    public function __construct(protected readonly array $replacement = [])
    {
    }

    abstract public function __invoke(): mixed;

    protected function replaceExternalId(string|int|float $externalId): string|int|float
    {
        if (!mb_strlen((string)$externalId) || !$this->replacement) {
            return $externalId;
        }

        foreach ($this->replacement as $dst => $src) {
            if (in_array($externalId, $src, true)) {
                return $dst;
            }
        }

        return $externalId;
    }

    protected function getHash(array $data): string
    {
        return md5(json_encode($data));
    }

    protected function getPremisesHash(MapperDto $dto): string
    {
        $data = $dto->result;
        $attachmentFields = [Premises::REF_IMAGE_PLAN];

        foreach ($attachmentFields as $fieldName) {
            if (empty($data[$fieldName]['path']) || !str_starts_with($data[$fieldName]['path'], $dto->historyDirPath)) {
                continue;
            }
            $data[$fieldName]['path'] = str_replace($dto->historyDirPath . '/', '', $data[$fieldName]['path']);
        }

        return $this->getHash($data);
    }
}
