<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Screens\Component;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\Page\Models\Contracts\ComponentDataProvider;
use Kelnik\Page\Models\Contracts\RouteProvider;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Platform\Screens\Screen;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;
use Orchid\Support\Facades\Toast;
use Symfony\Component\HttpFoundation\Response;

final class ComponentEditScreen extends Screen
{
    private bool $exists = false;
    private ?string $title = null;
    private int $pageId = 0;
    private int $siteId = 0;
    private ComponentDataProvider $dataProvider;

    public ?PageComponent $component = null;

    public function query(PageComponent $component): array
    {
        $component->load('page');
        $page = &$component->page;
        $this->setSiteAndPageId($page);

        abort_if($this->componentNotAvailable($component, $page), Response::HTTP_NOT_FOUND);

        $this->dataProvider = $component->data;
        $this->name = $this->dataProvider->getComponentTitle();
        $this->title = $this->dataProvider->getComponentTitleOriginal();
        $this->description = trans(
            'kelnik-page::admin.componentTitle',
            ['title' => $this->dataProvider->getComponentTitleOriginal()]
        );
        $this->exists = $component->exists;

        return $this->dataProvider->modifyQuery([
            'active' => $component->active,
            'data' => $this->dataProvider->toArray(),
            'page' => $page,
            'component' => $component,
            'componentName' => $component->component,
            'coreService' => $this->coreService
        ]);
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return $this->dataProvider->getCommandBar([
            Link::make(trans('kelnik-page::admin.componentsBack'))
                ->icon('bs.arrow-left-circle')
                ->route(
                    $this->coreService->getFullRouteName('page.components'),
                    ['site' => $this->siteId, 'page' => $this->pageId]
                ),

            Button::make(trans('kelnik-page::admin.delete'))
                ->icon('bs.trash3')
                ->method('removePageComponent')
                ->confirm(trans('kelnik-page::admin.componentDeleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ]);
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return $this->dataProvider->getEditLayouts();
    }

    public function saveData(Request $request): RedirectResponse
    {
        $this->component->load('page');
        $page = &$this->component->page;
        $this->setSiteAndPageId($page);

        abort_if($this->componentNotAvailable($this->component, $page), Response::HTTP_NOT_FOUND);

        if (method_exists($this->component->data, 'validateSavingRequest')) {
            // TODO: remove, deprecated
            $this->component->data->validateSavingRequest($this->component, $request);
        }
        $this->component->data->setDataFromRequest($this->component, $request);
        $this->component->active = $request->boolean('active');

        // Для сброса кеша, если явных изменений в полях не производилось.
        // Но, к примеру, изменили описание картинки в наборе изображений.
        $this->component->touch();

        if ($this->component->isDynamic()) {
            $this->component->load('routes');

            /** @var RouteProvider $routeProvider */
            $routeProvider = $this->component->component::initRouteProvider($page, $this->component);
            $routeProvider->validateSavingRequest($request);

            $this->pageLinkService->syncPageComponentRoutes(
                $this->component,
                $routeProvider->makeRoutesByParams($request->input('routes') ?? [])
            );
        }

        $this->component->data->afterSaveHandler();

        Toast::info(trans('kelnik-page::admin.pageComponentSaved'));

        return back();
    }

    public function removePageComponent(PageComponent $component): RedirectResponse
    {
        $component->load('page');
        $page = &$component->page;
        $this->setSiteAndPageId($page);

        abort_if($this->componentNotAvailable($component, $page), Response::HTTP_NOT_FOUND);

        resolve(PageComponentRepository::class)->delete($component)
            ? Toast::info(trans('kelnik-page::admin.pageComponentDeleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route(
            $this->coreService->getFullRouteName('page.components'),
            ['site' => $page->site_id, 'page' => $page->id]
        );
    }

    private function setSiteAndPageId(Page $page): void
    {
        $this->siteId = $page->site_id;
        $this->pageId = $page->getKey();
    }

    private function componentNotAvailable(PageComponent $pageComponent, Page $page): bool
    {
        return !$pageComponent->exists
            || !$page->exists
            || $page->getKey() !== $this->pageId
            || $page->site_id !== $this->siteId;
    }
}
