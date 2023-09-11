<?php

declare(strict_types=1);

namespace Kelnik\Estate\Services;

use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;

final class EstateService implements Contracts\EstateService
{
    public const PREMISES_DEFAULT_IMAGE = '/webicons/flat/plug.svg';

    public function __construct(
        private readonly PremisesRepository $premisesRepository,
        private readonly CoreService $coreService
    ) {
    }

    public function preparePremises(
        Collection $res,
        Collection $cardRoutes,
        ?Closure $callback = null
    ): Collection {
        if ($res->isEmpty()) {
            return $res;
        }

        $pageLinkService = resolve(PageLinkService::class);

        $res->each(function (Premises $el) use ($cardRoutes, $pageLinkService, $callback) {
            $el->typeTitle = $this->getTypeTitle($el);
            $el->typeShortTitle = $this->getShortTypeTitle($el);
            $el->imagePlanDefault = $this->hasDefaultPlanImage($el)
                ? $el->type->typeGroup->image->url()
                : self::PREMISES_DEFAULT_IMAGE;

            $cardRoute = $pageLinkService->getRouteNameByCategory($cardRoutes, $el->type->typeGroup->getKey());
            $routeParams = $cardRoute ? $pageLinkService->getRouteParams($cardRoute) : [];
            $routeName = null;

            if (is_string($cardRoute)) {
                $routeName = $cardRoute;
            } elseif ($cardRoute instanceof Route) {
                $routeName = $cardRoute->getName();
            }

            if ($routeParams && $routeName) {
                $params = [];
                foreach ($routeParams as $paramName) {
                    $params[$paramName] = $el->{$paramName} ?? null;
                }
                $el->routeName = $routeName;
                $el->url = route($routeName, $params, false);
                unset($params);
            }

            if ($callback) {
                $el = call_user_func($callback, $el);
            }
        });

        return $res;
    }

    public function getTypeTitle(Premises $premises): string
    {
        if (!$premises->type->typeGroup->living) {
            return $this->getNonResidentialTypeTitle($premises);
        }

        if (!$premises->type->typeGroup->build_title) {
            return $premises->title;
        }

        $wordFlat = ' ' . trans('kelnik-estate::front.premisesTypeTitle.flat');

        if (!$premises->type->rooms) {
            $wordFlat = '';
        }

        return $premises->type->title
            ? trans('kelnik-estate::front.premisesTypeTitle.simple', [
                'title' => $premises->type->title . $wordFlat,
                'number' => $premises->number ?: $premises->title
            ])
            : $premises->title;
    }

    public function getShortTypeTitle(Premises $premises): string
    {
        if (!$premises->type->typeGroup->living) {
            return $this->getNonResidentialTypeTitle($premises);
        }

        if (!$premises->type->typeGroup->build_title) {
            return $premises->title;
        }

        return $premises->type->rooms
            ? trans('kelnik-estate::front.premisesTypeTitle.shortWithRooms', [
                'rooms' => $premises->type->rooms,
                'number' => $premises->number ?: $premises->title
            ])
            : trans('kelnik-estate::front.premisesTypeTitle.short', [
                'title' => $premises->type->title,
                'number' => $premises->number ?: $premises->title
            ]);
    }

    public function getInternalTypeTitle(Premises $premises): string
    {
        if (!$premises->type->typeGroup->living) {
            return $this->getNonResidentialInternalTypeTitle($premises);
        }

        if (!$premises->type->title) {
            return $premises->title;
        }

        return $premises->type->rooms
            ? trans('kelnik-estate::front.premisesTypeTitle.shortWithRoomsInternal', [
                'rooms' => $premises->type->rooms,
                'number' => $premises->number ?: $premises->title,
                'area' => $premises->area_total
            ])
            : trans('kelnik-estate::front.premisesTypeTitle.shortInternal', [
                'title' => $premises->type->title,
                'number' => $premises->number ?: $premises->title,
                'area' => $premises->area_total
            ]);
    }

    private function getNonResidentialTypeTitle(Premises $premises): string
    {
        return $premises->number
            ? trans('kelnik-estate::front.premisesTypeTitle.nonResidential', [
                'title' => $premises->type->title,
                'number' => $premises->number ?: $premises->title
            ])
            : $premises->title;
    }

    private function getNonResidentialInternalTypeTitle(Premises $premises): string
    {
        return $premises->number
            ? trans('kelnik-estate::front.premisesTypeTitle.nonResidentialInternal', [
                'title' => $premises->type->title,
                'number' => $premises->number,
                'area' => $premises->area_total
            ])
            : $premises->title;
    }

    private function hasDefaultPlanImage(Premises $el): bool
    {
        return $el->relationLoaded('type')
            && $el->type->relationLoaded('typeGroup')
            && $el->type->typeGroup->image->exists;
    }

    public function createSlugByTitle(string $title): string
    {
        return Str::slug($title);
    }

    public function getModuleCacheTag(): string
    {
        return EstateServiceProvider::MODULE_NAME ?? 'estate';
    }

    public function getPremisesCacheTag(int|string $id): ?string
    {
        return $this->getCacheTag($id, 'estate_premises');
    }

    private function getCacheTag(int|string $id, string $prefix): ?string
    {
        return $id ? $prefix . '_' . $id : null;
    }
}
