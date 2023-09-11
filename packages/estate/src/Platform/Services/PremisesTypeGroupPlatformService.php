<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Events\EstateModelEvent;
use Kelnik\Estate\Models\PremisesType;
use Kelnik\Estate\Models\PremisesTypeGroup;
use Kelnik\Estate\Repositories\Contracts\PremisesTypeGroupRepository;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCard;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCardLinkDto;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Orchid\Support\Facades\Toast;

final class PremisesTypeGroupPlatformService implements Contracts\PremisesTypeGroupPlatformService
{
    public function __construct(
        private readonly PremisesTypeGroupRepository $groupRepository,
        private readonly CoreService $coreService
    ) {
    }

    public function getList(): array
    {
        $res = [];

        $this->groupRepository
            ->getAllWithTypes()
            ->each(static function (PremisesTypeGroup $el) use (&$res) {
                return $el->types->each(function (PremisesType $type) use ($el, &$res) {
                    $res[$type->getKey()] = $el->title . ': ' . $type->title;
                });
            });

        return $res;
    }

    public function save(PremisesTypeGroup $premisesTypeGroup, Request $request): RedirectResponse
    {
        $request->validate([
            'type.title' => 'required|max:255',
            'type.slug' => 'nullable|max:255|regex:/^[a-z0-9\-_.]+$/i',
            'type.living' => 'boolean',
            'type.build_title' => 'boolean',
            'type.external_id' => 'nullable|max:255',
            'type.plural.*' => 'nullable|max:100'
        ]);

        $typeGroupData = Arr::get($request->only('type'), 'type');
        $typeGroupData['plural'] = !empty($typeGroupData['plural'])
            ? array_filter($typeGroupData['plural'])
            : [];
        $types = $typeGroupData['types'] ?? [];
        unset($typeGroupData['types']);

        $premisesTypeGroup->fill($typeGroupData);

        if ($premisesTypeGroup->slug !== null && !$this->groupRepository->isUnique($premisesTypeGroup)) {
            return back()->withErrors(new MessageBag([
                trans('validation.unique', ['attribute' => trans('kelnik-estate::admin.slug')])
            ]));
        }

        // Types
        if ($types) {
            $request->validate(
                [
                    'type.types.*.title' => 'required|max:255',
                    'type.types.*.slug' => 'max:255|regex:/^[a-z0-9\-_]+$/i',
                    'type.types.*.room' => 'nullable|numeric',
                    'type.types.*.replace_id' => 'nullable|numeric'
                ]
            );

            foreach ($types as &$v) {
                $v['rooms'] = (int) ($v['rooms'] ?? 0);
            }
            unset($v);
        }

        $this->groupRepository->save($premisesTypeGroup, $types);
        $this->createLinkToPage($premisesTypeGroup, $request->input('page'));

        Toast::info(trans('kelnik-contact::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('estate.ptype.list'));
    }

    public function remove(PremisesTypeGroup $premisesTypeGroup, string $backRoute): RedirectResponse
    {
        $this->groupRepository->delete($premisesTypeGroup)
            ? Toast::info(trans('kelnik-estate::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route($this->coreService->getFullRouteName($backRoute));
    }

    public function createLinkToPage(PremisesTypeGroup $premisesTypeGroup, array $sitePages): void
    {
        if (!$this->coreService->hasModule('page') || !class_exists(PremisesCard::class)) {
            return;
        }

        /** @var PageLinkService $pageLinkService */
        $pageLinkService = resolve(PageLinkService::class);
        $dynComponentDto = new PremisesCardLinkDto($premisesTypeGroup->title, $premisesTypeGroup->slug);
        $dynComponentDto->routePrefix = $premisesTypeGroup->slug;

        $linkModified = $pageLinkService->createOrUpdateLinkDynComponentToPage(
            $sitePages,
            $dynComponentDto,
            $premisesTypeGroup::class,
            $premisesTypeGroup->getKey()
        );

        if ($linkModified) {
            EstateModelEvent::dispatch($premisesTypeGroup, EstateModelEvent::UPDATED);
        }
    }

    public function createSlugByTitle(string $title): string
    {
        return Str::slug($title);
    }
}
