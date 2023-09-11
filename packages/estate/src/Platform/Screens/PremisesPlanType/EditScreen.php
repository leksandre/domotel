<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens\PremisesPlanType;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Kelnik\Estate\Models\PremisesPlanType;
use Kelnik\Estate\Platform\Layouts\PremisesPlanType\EditLayout;
use Kelnik\Estate\Platform\Screens\BaseScreen;
use Kelnik\Estate\Repositories\Contracts\PremisesPlanTypeRepository;
use Kelnik\Estate\Platform\Services\Contracts\PremisesPlanTypePlatformService;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layout;

final class EditScreen extends BaseScreen
{
    private bool $exists = false;
    private ?string $title = null;
    protected string $routeToList = 'estate.pplantype.list';
    private PremisesPlanTypePlatformService $premisesPlanTypePlatformService;

    public ?PremisesPlanType $type = null;

    public function __construct()
    {
        parent::__construct();
        $this->premisesPlanTypePlatformService = resolve(PremisesPlanTypePlatformService::class);
    }

    public function query(PremisesPlanType $type): array
    {
        $this->name = trans('kelnik-estate::admin.menu.premisesPlanTypes');
        $this->exists = $type->exists;

        if ($this->exists) {
            $this->name = $type->title;
        }

        return [
            'type' => $type
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

    public function transliterate(Request $request, PremisesPlanTypeRepository $repository): JsonResponse
    {
        $res = [
            'state' => false,
            'slug' => $request->get('slug')
        ];

        $title = $request->get('source');

        if ($request->get('action') === 'transliterate') {
            $res['slug'] = $title ? $this->premisesPlanTypePlatformService->createSlugByTitle($title) : '';
        }

        $typeGroup = $repository->findByPrimary((int)$request->get('sourceId'));

        $typeGroup->title = $title;
        $typeGroup->slug = $request->get('slug') ?? $res['slug'];

        $res['state'] = $repository->isUnique($typeGroup);

        return Response::json($res);
    }

    public function saveRow(Request $request): RedirectResponse
    {
        return $this->premisesPlanTypePlatformService->save($this->type, $request);
    }

    public function removeRow(PremisesPlanType $type): RedirectResponse
    {
        return $this->premisesPlanTypePlatformService->remove($type, $this->routeToList);
    }
}
