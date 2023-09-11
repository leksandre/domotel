<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Kelnik\News\Models\Element;
use Kelnik\News\Platform\Layouts\Element\BaseLayout;
use Kelnik\News\Platform\Layouts\Element\ContentLayout;
use Kelnik\News\Platform\Layouts\Element\MetaLayout;
use Kelnik\News\Platform\Layouts\Element\PreviewLayout;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;

class ElementEditScreen extends Screen
{
    protected bool $exists = false;
    protected ?string $title = null;
    protected int $categoryId = 0;

    public ?Element $element = null;

    public function query(Element $element): array
    {
        $this->name = trans('kelnik-news::admin.menu.title');
        $this->exists = $element->exists;
        $this->categoryId = (int)Route::current()->parameter('category_id');

        if ($this->exists) {
            $this->name = $element->title;
        }

        return [
            'coreService' => $this->coreService,
            'category_id' => $this->categoryId,
            'element' => $element
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-news::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('news.elements')),

            Button::make(trans('kelnik-news::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeElement')
                ->confirm(trans('kelnik-news::admin.deleteConfirm', ['title' => $this->name]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
            \Orchid\Support\Facades\Layout::tabs([
                 trans('kelnik-news::admin.tab.base') => BaseLayout::class,
                 trans('kelnik-news::admin.tab.preview') => PreviewLayout::class,
                trans('kelnik-news::admin.tab.content') => ContentLayout::class,
                trans('kelnik-news::admin.tab.meta') => MetaLayout::class,
            ])
        ];
    }

    public function saveElement(Request $request): RedirectResponse
    {
        return $this->newsPlatformService->saveElement($this->element, $request);
    }

    public function removeElement(Element $element, Request $request): RedirectResponse
    {
        resolve(ElementRepository::class)->delete($element)
            ? Toast::info(trans('kelnik-news::admin.deleted'))
            : Toast::warning('An error has occurred');

        $this->categoryId = (int)Route::current()->parameter('category');

        return $this->categoryId
            ? redirect()->route($this->coreService->getFullRouteName(
                'news.category.elements'),
                 ['category' => $this->categoryId]
             )
            : redirect()->route($this->coreService->getFullRouteName('news.elements'));
    }

    public function transliterate(Request $request): JsonResponse
    {
        $res = [
            'state' => false,
            'slug' => $request->get('slug')
        ];

        $title = $request->get('source');

        if ($request->get('action') === 'transliterate') {
            $res['slug'] = $title ? $this->newsPlatformService->createSlugByTitle($title) : '';
        }

        $repository = resolve(ElementRepository::class);
        $element = $repository->findByPrimary((int) $request->get('sourceId'));

        $element->title = $title;
        $element->slug = $request->get('slug') ?? $res['slug'];

        $res['state'] = $repository->isUnique($element);

        return Response::json($res);
    }
}
