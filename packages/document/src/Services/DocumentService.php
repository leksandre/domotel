<?php

declare(strict_types=1);

namespace Kelnik\Document\Services;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kelnik\Core\Helpers\FileHelper;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Document\Dto\ElementSortDto;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Models\Element;
use Kelnik\Document\Repositories\Contracts\CategoryRepository;
use Kelnik\Document\Repositories\Contracts\ElementRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;

final class DocumentService implements Contracts\DocumentService
{
    public function __construct(
        private CategoryRepository $categoryRepo,
        private ElementRepository $elementRepo,
        private CoreService $coreService
    ) {
    }

    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-document::admin.documentElements'))
            ->route(
                $this->coreService->getFullRouteName('document.categories')
            )
            ->icon('info')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px');
    }

    public function prepareList(Collection $categories, ?Closure $callback = null): Collection
    {
        if ($categories->isEmpty()) {
            return $categories;
        }

        $dateFormat = config('kelnik-document.dateFormat', 'j F Y');

        return $categories->each(static function (Category $category) use ($dateFormat) {
            $category->elements->each(static function (Element $el) use ($dateFormat) {
                $el->publishDateFormatted = $el->publish_date?->translatedFormat($dateFormat);
                $el->attachmentExtension = Str::lower($el->attachment->extension);
                $el->attachmentSize = (float)$el->attachment->size;
                $el->attachmentSizeFormatted = FileHelper::sizeFormat((float)$el->attachment->size);
                $el->attachmentUrl = $el->attachment->url() ?? '';
                $el->unsetRelation('attachment');
            });
        });
    }

    public function sortCategories(ElementSortDto $dto): bool
    {
        $elements = $this->categoryRepo->getAll();

        if ($elements->isEmpty()) {
            return false;
        }

        $elements->each(function (Category $el) use ($dto) {
            $el->priority = (int)array_search($el->getKey(), $dto->elements) + $dto->defaultPriority;
            $this->categoryRepo->save($el);
        });

        return true;
    }

    public function createSlugByTitle(string $title): string
    {
        return Str::slug($title);
    }

    public function getCacheTag(): string
    {
        return 'documents';
    }

    public function getGroupCacheTag(int|string $id): string
    {
        return 'documentGroup_' . $id;
    }
}
