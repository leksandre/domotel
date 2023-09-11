<?php

declare(strict_types=1);

namespace Kelnik\Document\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\MessageBag;
use Kelnik\Document\Http\Requests\CategorySaveRequest;
use Kelnik\Document\Http\Requests\TranslitirateRequest;
use Kelnik\Document\Models\Category;
use Kelnik\Document\Platform\Layouts\Category\EditLayout;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

final class CategoryEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;
    public ?Category $category = null;

    /**
     * @param Category $category
     *
     * @return array
     */
    public function query(Category $category): array
    {
        $this->name = trans('kelnik-document::admin.menu.title');
        $this->exists = $category->exists;

        if ($this->exists) {
            $this->name = $category->title;
        }

        return [
            'category' => $category,
            'elements' => $category->elements
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-document::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('document.categories')),

            Button::make(trans('kelnik-document::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeCategory')
                ->confirm(trans('kelnik-document::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            EditLayout::class
        ];
    }

    public function saveCategory(CategorySaveRequest $request): RedirectResponse
    {
        $dto = $request->getDto();
        $category = $this->category;

        $category->title = $dto->title;
        $category->active = $dto->active;
        $category->user()->associate($dto->user);
        $category->group()->dissociate();
        $category->slug = $dto->slug;

        if ($dto->group) {
            $category->group()->associate($dto->group);
        }

        unset($categoryData);

        if (!$this->categoryRepository->isUnique($category)) {
            return back()->withErrors(
                new MessageBag([
                    trans('validation.unique', ['attribute' => trans('kelnik-document::admin.title')])
                ])
            );
        }
        $this->categoryRepository->save($category, $dto->elements);

        Toast::info(trans('kelnik-document::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('document.categories'));
    }

    public function removeCategory(Category $category): RedirectResponse
    {
        $this->categoryRepository->delete($category)
            ? Toast::info(trans('kelnik-document::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('document.categories'));
    }

    public function transliterate(TranslitirateRequest $request): JsonResponse
    {
        $dto = $request->getDto();

        $res = [
            'state' => false,
            'slug' => $dto->slug
        ];

        $title = $dto->source;

        if ($dto->action === 'transliterate') {
            $res['slug'] = $title ? $this->documentService->createSlugByTitle($title) : '';
        }

        $category = $this->categoryRepository->findByPrimary($dto->sourceId);

        $category->title = $title;
        $category->slug = $dto->slug ?? $res['slug'];

        $res['state'] = $this->categoryRepository->isUnique($category);

        return Response::json($res);
    }
}
