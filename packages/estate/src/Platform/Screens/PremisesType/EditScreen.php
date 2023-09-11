<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesType;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Platform\Layouts\PremisesType\EditLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeRepository;
use Kelnik\Estate\Platform\Services\Contracts\PremisesTypeGroupPlatformService;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCard;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.ptype.list';
    private PremisesTypeGroupPlatformService $premisesTypeGroupPlatformService;

    public ?PremisesTypeGroup $type = null;

    public function __construct()
    {
        parent::__construct();
        $this->premisesTypeGroupPlatformService = resolve(PremisesTypeGroupPlatformService::class);
    }

    public function query(PremisesTypeGroup $type): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesTypes');
        $this->exists = $type->exists;
        $type->load('types');

        if ($this->exists) {
            $this->name = $type->title;
        }

        $pageIds = [];
        $pageLinkService = resolve(PageLinkService::class);

        if ($type->exists) {
            $pageIds = $pageLinkService->getPagesWithDynComponentByRouteElement(
                PremisesTypeGroup::class,
                $type->getKey()
            );
        }

        return [
            'type' => $type,
            'types' => resolve(PremisesTypeRepository::class)->getAll()->pluck('title', 'id'),
            'pageOptions' => $pageLinkService->getOptionListOfPagesWithDynComponent([PremisesCard::class]),
            'pageIds' => $pageIds,
            'coreService' => $this->coreService
        ];
    }

    /** @return Action[] */
    public function commandBar(): array
    {
        return [
            Link::make(trans('kelnik-estate::admin.back'))
                ->icon('bs.arrow-left-circle')
                ->route($this->coreService->getFullRouteName($this->routeToList)),

            Button::make(trans('kelnik-estate::admin.delete'))
                ->icon('bs.trash3')
                ->method('removeRow')
                ->confirm(trans('kelnik-estate::admin.deleteConfirm', ['title' => $this->title]))
                ->canSee($this->exists),
        ];
    }

    /** @return Layout[] */
    public function layout(): array
    {
        return [
             EditLayout::class
        ];
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->premisesTypeGroupPlatformService->save($this->type, $request);
    }

    public function removeRow(PremisesTypeGroup $type): RedirectResponse
    {
        return $this->premisesTypeGroupPlatformService->remove($type, $this->routeToList);
    }

    public function transliterate(Request $request): JsonResponse
    {
        return $this->transliterateModel($request, resolve(PremisesTypeGroupRepository::class));
    }

    public function transliterateType(Request $request): JsonResponse
    {
       return $this->transliterateModel($request, resolve(PremisesTypeRepository::class));
    }

    private function transliterateModel(
        Request $request,
        PremisesTypeGroupRepository|PremisesTypeRepository $repository
    ): JsonResponse {
        $res = [
            'state' => false,
            'slug' => $request->get('slug')
        ];

        $title = $request->get('source');

        if ($request->get('action') === 'transliterate') {
            $res['slug'] = $title ? $this->premisesTypeGroupPlatformService->createSlugByTitle($title) : '';
        }

        $typeGroup = $repository->findByPrimary((int)$request->get('sourceId'));

        $typeGroup->title = $title;
        $typeGroup->slug = $request->get('slug') ?? $res['slug'];

        $res['state'] = $repository->isUnique($typeGroup);

        return Response::json($res);
    }
}
