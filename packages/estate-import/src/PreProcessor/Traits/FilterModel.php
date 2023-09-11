<?php

declare(strict_types=1);

namespace Kelnik\EstateImport\PreProcessor\Traits;

use Illuminate\Support\MessageBag;
use Kelnik\EstateImport\PreProcessor\Contracts\Filter;

trait FilterModel
{
    protected function filterModel(string $model, array $data, ?Filter $filter): bool
    {
        $tmpData = $data;
        unset($tmpData['hash']);

        if (!array_filter($tmpData)) {
            $this->logger->debug('Model is empty. Skipping.', ['Model' => $model]);
            return false;
        }

        if ($this->rowExists($model, $data)) {
            $this->logger->debug(
                'Model exists. Skipping.',
                [
                    'Model' => $model,
                    'data' => $data
                ]
            );
            return false;
        }

        if ($filter && ($filterRes = $filter($model, $data)) !== true) {
            $this->logger->debug(
                'Model filtered and skipped',
                [
                    'message' => $filterRes instanceof MessageBag ? $filterRes->messages() : null,
                    'Model' => $model,
                    'data' => $data
                ]
            );
            return false;
        }

        return true;
    }
}
