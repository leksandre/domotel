<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Screens\Page;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Kelnik\Page\Models\Enums\RedirectType;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Platform\Exceptions\PageNotFound;
use Kelnik\Page\Platform\Layouts\Page\EditLayout;
use Kelnik\Page\Platform\Layouts\Page\MetaLayout;
use Kelnik\Page\Platform\Screens\Screen;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;
    private int $siteId = 0;

    public ?Page $page = null;

    public function query(Page $page): array
    {
        $this->name = trans('kelnik-page::admin.menuTitle');
        $siteId = (int)Route::current()->parameter('site');

        abort_if(
            $page->exists && $page->site_id !== $siteId,
            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
        );

        $this->exists = $page->exists;
        $this->siteId = $siteId;

        if ($this->exists) {
            $this->title = $page->title;
        }

        $rTypes = [];
        foreach (RedirectType::cases() as $v) {
            $rTypes[$v->value] = $v->title();
        }

        return [
            'page' => $page,
            'redirect_types' => $rTypes
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-page::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route(
                    $this->coreService->getFullRouteName('page.list'),
                    ['site' => $this->siteId]
                ),

            Button::make(trans('kelnik-page::admin.delete'))
                ->icon('bs.trash3')
                ->method('removePage')
                ->confirm(trans('kelnik-page::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            \Orchid\Support\Facades\Layout::tabs([
                trans('kelnik-page::admin.tabs.base') => EditLayout::class,
                trans('kelnik-page::admin.tabs.meta') => MetaLayout::class
            ])
        ];
    }

    public function savePage(Request $request): RedirectResponse
    {
        try {
            return $this->pagePlatformService->save(
                (int)$request->route()->parameter('site', 0),
                (int)$request->route()->parameter('page', 0),
                $request->input('page')
            );
        } catch (PageNotFound $e) {
            abort(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
    }

    public function removePage(Request $request): RedirectResponse
    {
        try {
            return $this->pagePlatformService->delete(
                (int)$request->route()->parameter('site', 0),
                (int)$request->route()->parameter('page', 0)
            );
        } catch (PageNotFound $e) {
            abort(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
    }

    public function transliterate(Request $request): JsonResponse
    {
        $res = [
            'slug' => $request->get('slug')
        ];

        $title = $request->get('source');

        if ($request->get('action') === 'transliterate') {
            $res['slug'] = $title ? $this->pagePlatformService->createSlugByTitle($title) : '';
        }

        $res['state'] = $this->pagePlatformService->isUnique(
            (int)$request->route()->parameter('site'),
            (int)$request->route()->parameter('page'),
            $res['slug']
        );

        return Response::json($res);
    }
}
