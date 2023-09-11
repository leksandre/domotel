<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Filters\Contracts;

use Illuminate\Support\Collection;
use Kelnik\EstateVisual\Repositories\Contracts\SearchRepository;

abstract class AbstractFilter implements Filter
{
    public const TYPE_BASE = 'base';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_SLIDER = 'slider';
    public const TYPE_BUTTON = 'button';

    public const PARAM_TYPES = 'types';
    public const PARAM_STATUSES = 'statuses';

    protected array $requestValues = [];
    protected array $additionalValues = [];
    protected array $excludeParams = [];
    protected readonly SearchRepository $repository;
    protected readonly Collection $selectorSettings;

    public function __construct(?Collection $selectorSettings = null)
    {
        $this->repository = resolve(SearchRepository::class);
        $this->selectorSettings = $selectorSettings ?? new Collection();
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function isHidden(): bool
    {
        return false;
    }

    public function setRequestValues(array $values): void
    {
        $this->requestValues = $values;
    }

    public function setAdditionalValues(array $values): void
    {
        $this->additionalValues = $values;
    }

    public function setExcludeParams(array $params): void
    {
        $this->excludeParams = $params;
    }
}
