<?php

declare(strict_types=1);

namespace Kelnik\News\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Kelnik\News\Models\Category;
use Kelnik\News\Platform\Layouts\Category\EditLayout;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Kelnik\News\View\Components\ElementCard\ElementCard;
use Kelnik\Page\Services\Contracts\PageLinkService;
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

    public function query(Category $category): array
    {
        $this->name = trans('kelnik-news::admin.menu.title');
        $this->exists = $category->exists;

        if ($this->exists) {
            $this->name = $category->title;
        }

        $pageIds = [];
        $pageLinkService = resolve(PageLinkService::class);

        if ($category->exists) {
            $pageIds = $pageLinkService->getPagesWithDynComponentByRouteElement(
                Category::class,
                $category->getKey()
            );
        }

        return [
            'coreService' => $this->coreService,
            'category' => $category,
            'pageOptions' => $pageLinkService->getOptionListOfPagesWithDynComponent([ElementCard::class]),
            'pageIds' => $pageIds
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-news::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName('news.categories')),

            Button::make(trans('kelnik-news::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeCategory')
                ->confirm(trans('kelnik-news::admin.deleteConfirm', ['title' => $this->title]))
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

    public function saveCategory(Request $request): RedirectResponse
    {
        return $this->newsPlatformService->saveCategory($this->category, $request);
    }

    public function removeCategory(Category $category): RedirectResponse
    {
        resolve(CategoryRepository::class)->delete($category)
            ? Toast::info(trans('kelnik-news::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName('news.categories'));
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

        $repository = resolve(CategoryRepository::class);
        $category = $repository->findByPrimary((int) $request->get('sourceId'));

        $category->title = $title;
        $category->slug = $request->get('slug') ?? $res['slug'];

        $res['state'] = $repository->isUnique($category);

        return Response::json($res);
    }
}
