<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Filters\Contracts;

use Kelnik\EstateSearch\Repositories\Contracts\SearchRepository;

abstract class AbstractFilter implements Filter
{
    public const TYPE_BASE = 'base';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_TOGGLE = 'toggle';
    public const TYPE_SLIDER = 'slider';

    protected const TITLE = '';
    protected const ADMIN_TITLE = '';

    protected array $requestValues = [];
    protected array $additionalValues = [];
    protected ?string $title = null;
    protected SearchRepository $repository;

    public function __construct()
    {
        $this->repository = resolve(SearchRepository::class);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function getTypeTitle(): string
    {
        return trans('kelnik-estate-search::admin.form.types.' . $this->getType());
    }

    public function getAdminTitle(): string
    {
        return (static::ADMIN_TITLE ? trans(static::ADMIN_TITLE) : '-') . ' (' . $this->getTypeTitle() . ')';
    }

    public function setTitle(?string $title = null): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title ?? (static::TITLE ? trans(static::TITLE) : '');
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
}
