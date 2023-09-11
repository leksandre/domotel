<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Screens\Component;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Platform\Layouts\PageComponents\ContentListLayout;
use Kelnik\Page\Platform\Layouts\PageComponents\FooterListLayout;
use Kelnik\Page\Platform\Layouts\PageComponents\HeaderListLayout;
use Kelnik\Page\Platform\Layouts\PageComponents\NullListLayout;
use Kelnik\Page\Platform\Layouts\PageComponents\PageComponentModalLayout;
use Kelnik\Page\Platform\Screens\Screen;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponentSection;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

final class ComponentsListScreen extends Screen
{
    public function query(): array
    {
        $pageId = (int)Route::current()->parameter('page');
        $siteId = (int)Route::current()->parameter('site');

        /** @var Page $page */
        $page = resolve(PageRepository::class)->findByPrimary($pageId);

        abort_if(
            !$page->exists || $page->site_id !== $siteId,
            \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
        );

        $this->name = $page->title;

        $res = [
            'coreService' => $this->coreService,
            'page' => $page,
            'components' => [
                KelnikPageComponentSection::PAGE_COMPONENT_SECTION_HEADER => new Collection(),
                KelnikPageComponentSection::PAGE_COMPONENT_SECTION_CONTENT => new Collection(),
                KelnikPageComponentSection::PAGE_COMPONENT_SECTION_FOOTER => new Collection(),
                KelnikPageComponentSection::PAGE_COMPONENT_SECTION_NULL => new Collection()
            ],
            'sortableUrl' => route(
                $this->coreService->getFullRouteName('page.components.sort'),
                ['site' => $siteId, 'page' => $page->id],
                false
            )
        ];

        $page->components->each(static function (PageComponent $pageComponent) use (&$res) {
            $section = KelnikPageComponentSection::PAGE_COMPONENT_SECTION_NULL;

            if (class_exists($pageComponent->component)) {
                $section = $pageComponent->component::getPageComponentSection()
                    ?? KelnikPageComponentSection::PAGE_COMPONENT_SECTION_HEADER;
            }

            $res['components'][$section]->add($pageComponent);
        });

        return $res;
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        $curRoute = Route::current();

        return [
            ModalToggle::make(trans('kelnik-page::admin.addComponent'))
                ->modal('addPageComponent')
                ->modalTitle(trans('kelnik-page::admin.addComponentHeader'))
                ->action(route(
                    $curRoute->getName(),
                    [
                        'site' => $curRoute->parameter('site'),
                        'page' => $curRoute->parameter('page'),
                        'method' => 'addComponent'
                    ]
                ))
                ->icon('bs.plus-circle')
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            Layout::modal(
                'addPageComponent',
                [PageComponentModalLayout::class]
            ),
            HeaderListLayout::class,
            ContentListLayout::class,
            FooterListLayout::class,
            NullListLayout::class
        ];
    }

    public function addComponent(Request $request): void
    {
        try {
            $res = $this->pagePlatformService->addComponent(
                (int)Route::current()->parameter('site'),
                (int)Route::current()->parameter('page'),
                (string)$request->get('component_id')
            );
        } catch (InvalidArgumentException $e) {
            Toast::error($e->getMessage());
            return;
        }

        if ($res) {
            Toast::success(trans('kelnik-page::admin.pageComponentAdded'));
            return;
        }

        Toast::error(trans('kelnik-page::admin.error'));
    }

    public function sortable(Request $request): JsonResponse
    {
        $siteId = (int) $request->route()->parameter('site', 0);
        $pageId = (int) $request->route()->parameter('page', 0);
        $components = array_values($request->input('elements', []));

        if (!$components) {
            return Response::json([
                'success' => false,
                'messages' => [trans('kelnik-page::admin.error.emptyList')]
            ]);
        }

        foreach ($components as $k => &$v) {
            $v = (int) $v;
            if (!$v) {
                unset($components[$k]);
            }
        }

        $this->pagePlatformService->sortComponents($siteId, $pageId, $components);

        return Response::json([
            'success' => true,
            'messages' => [trans('kelnik-page::admin.success')]
        ]);
    }
}
