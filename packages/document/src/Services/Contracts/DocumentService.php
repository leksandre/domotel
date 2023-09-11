<?php

declare(strict_types=1);

namespace Kelnik\Document\Services\Contracts;

use Closure;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Document\Dto\ElementSortDto;
use Kelnik\Document\Repositories\Contracts\CategoryRepository;
use Kelnik\Document\Repositories\Contracts\ElementRepository;
use Orchid\Screen\Field;

interface DocumentService
{
    public function __construct(
        CategoryRepository $categoryRepo,
        ElementRepository $elementRepo,
        CoreService $coreService
    );

    public function getContentLink(): Field;

    public function prepareList(Collection $categories, ?Closure $callback = null): Collection;

    public function sortCategories(ElementSortDto $dto): bool;

    public function getCacheTag(): string;

    public function getGroupCacheTag(int|string $id): string;
}
