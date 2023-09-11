<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\News\Events\CategoryEvent;
use Kelnik\News\Models\Category;
use Kelnik\News\Models\Contracts\ElementButton;
use Kelnik\News\Models\Element;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Kelnik\News\View\Components\ElementCard\ElementCard;
use Kelnik\News\View\Components\ElementCard\ElementCardLinkDto;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;
use Orchid\Support\Facades\Toast;

final class NewsPlatformService implements Contracts\NewsPlatformService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepo,
        private readonly ElementRepository $elementRepo,
        private readonly CoreService $coreService,
        private readonly PageLinkService $pageLinkService
    ) {
    }

    public function saveCategory(Category $category, Request $request): RedirectResponse
    {
        $categoryData = $request->only([
            'category.title',
            'category.slug',
            'category.active',
            'category.priority'
        ]);

//        $formSlug = $request->input('category.slug');
//        if (($category->exists && $category->slug !== $formSlug) || !$category->exists && $formSlug) {
//            $request->validate([
//               'category.slug' => 'nullable|max:255|regex:/^[a-z0-9\-_.]+$/i'
//            ]);
//        }

        $categoryData = Arr::get($categoryData, 'category');
        $category->fill($categoryData);
        $category->slug = Str::slug($category->title);
        $category->user()->associate(auth()->user());
        unset($categoryData);

        if (!$this->categoryRepo->isUnique($category)) {
            return back()->withErrors(
                new MessageBag([
                   trans('validation.unique', ['attribute' => trans('kelnik-news::admin.title')])
                ])
            );
        }

        $this->categoryRepo->save($category);
        $this->createLinkToPage($category, $request->input('page'));

        Toast::info(trans('kelnik-news::admin.saved'));

        return redirect()->route(
            $this->coreService->getFullRouteName('news.categories')
        );
    }

    public function createLinkToPage(Category $category, array $sitePages): void
    {
        if (!$this->coreService->hasModule('page') || !class_exists(ElementCard::class)) {
            return;
        }
        $dynComponentDto = new ElementCardLinkDto($category->title, $category->slug);
        $dynComponentDto->routePrefix = $category->slug;

        $linkModified = $this->pageLinkService->createOrUpdateLinkDynComponentToPage(
            $sitePages,
            $dynComponentDto,
            $category::class,
            $category->getKey()
        );

        if ($linkModified) {
            CategoryEvent::dispatch($category, CategoryEvent::UPDATED);
        }
    }

    public function saveElement(Element $element, Request $request): RedirectResponse
    {
        $elementData = $request->only('element');
        $rules = [
            'element.active_date_start' => 'nullable|date',
            'element.active_date_finish' => 'nullable|date',
            'element.publish_date_start' => 'nullable|date',
            'element.publish_date_finish' => 'nullable|date',
            'element.publish_date' => 'nullable|date',
        ];

        $formSlug = $request->input('element.slug');
        if (($element->exists && $element->slug !== $formSlug) || !$element->exists && $formSlug) {
            $rules['element.slug'] = 'required|max:255|regex:/^[a-z0-9\-_.]+$/i';
        }

        $request->validate($rules);

        $elementData = Arr::get($elementData, 'element');
        $elementData = Arr::except($elementData, ['category_id', 'button', 'images', 'meta']);
        $element->fill($elementData);
        $element->user()->associate(auth()->user());

        if (!$this->elementRepo->isUnique($element)) {
            return back()->withErrors(new MessageBag([
               trans('validation.unique', ['attribute' => trans('kelnik-news::admin.slug')])
            ]));
        }

        $category = $request->input('element.category_id');

        if ($category && $category = Category::find($category)) {
            $element->category()->associate($category);
        }

        $element->button = null;
        $button = $request->input('element.button', []);

        if (!empty($button['text']) || !empty($button['link'])) {
            $element->button = resolve(
                ElementButton::class,
                [
                    'link' => $button['link'] ?? null,
                    'text' => $button['text'] ?? null,
                    'target' => !empty($button['target']) ? ElementButton::EXTERNAL_TARGET : null
                ]
            );
        }

        $element->meta->fill($request->input('element.meta', []));

        $this->elementRepo->save($element, $request->input('element.images') ?? []);

        Toast::info(trans('kelnik-news::admin.saved'));

        return str_contains(Route::current()->getName(), 'category.')
                ? redirect()->route(
                    $this->coreService->getFullRouteName('news.category.elements'),
                    ['category' => $element->category_id]
                )
                : redirect()->route($this->coreService->getFullRouteName('news.elements'));
    }

    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-news::admin.newsElements'))
                ->route($this->coreService->getFullRouteName('news.elements'))
                ->icon('bs.info')
                ->class('btn btn-info')
                ->target('_blank')
                ->style('display: inline-block; margin-bottom: 20px');
    }

    public function createSlugByTitle(string $title): string
    {
        return Str::slug($title);
    }
}
