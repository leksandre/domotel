<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Services\Contracts;

use Illuminate\Support\Collection;
use Kelnik\EstateSearch\Models\Contracts\SearchConfig;
use Kelnik\EstateSearch\Models\Filters\Contracts\Filter;
use Kelnik\EstateSearch\Models\Orders\Contracts\Order;

interface SearchService
{
    public const PARAM_TYPES = 'types';
    public const PARAM_STATUSES = 'statuses';

    public function __construct(SearchConfig $config);

    public function addFilter(Filter $filter): void;

    public function addOrder(Order $order): void;

    public function getForm(array $request): Collection;

    public function getResults(array $request): Collection;

    public function getAllResults(array $request): Collection;

    public function getCount(array $request): int;

    public function getConfig(): ?SearchConfig;
}
