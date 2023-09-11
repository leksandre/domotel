<?php

declare(strict_types=1);

namespace Kelnik\News\Services;

use Closure;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Kelnik\News\Models\Element;
use Kelnik\News\Repositories\Contracts\CategoryRepository;
use Kelnik\News\Repositories\Contracts\ElementRepository;
use Kelnik\Page\Services\Contracts\PageLinkService;
use Orchid\Attachment\Models\Attachment;

final class NewsService implements Contracts\NewsService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepo,
        private readonly ElementRepository $elementRepo,
        private readonly CoreService $coreService,
        private readonly PageLinkService $pageLinkService
    ) {
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepo->getAll();
    }

    public function getActiveRowByPrimary(int|string $primary, ?array $fields = null): Element
    {
        return $this->elementRepo->findActiveByPrimary($primary, $fields);
    }

    public function getListByElement(
        int|array $categoryId,
        int $excludeId,
        int $count,
        Collection $cardRoutes = null
    ): Collection {
        if (!is_array($categoryId)) {
            $categoryId = [$categoryId];
        }

        $res = $this->elementRepo->getListWithExcludedRow($categoryId, $excludeId, $count);

        return $this->prepareElements($res, $cardRoutes ?? new Collection());
    }

    public function prepareElements(
        Collection $res,
        Collection $cardRoutes,
        ?Closure $callback = null
    ): Collection {
        if ($res->isEmpty()) {
            return $res;
        }

        $dateFormat = config('kelnik-news.dateFormat', 'j F Y');

        $res->each(function (Element $el) use ($dateFormat, $cardRoutes, $callback) {
            $el->publishDateFormatted = $el->publish_date?->translatedFormat($dateFormat);
            $el->publishDateStartFormatted = $el->publish_date_start?->translatedFormat($dateFormat);
            $el->publishDateFinishFormatted = $el->publish_date_finish?->translatedFormat($dateFormat);

            $cardRoute = $this->pageLinkService->getRouteNameByCategory($cardRoutes, $el->category->getKey());
            $routeParams = $cardRoute ? $this->pageLinkService->getRouteParams($cardRoute) : [];
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

            $hasPictureModule = $this->coreService->hasModule('image');

            if ($el->relationLoaded('images') && $el->images->isNotEmpty()) {
                $code = 'publication-' . $el->id;
                $el->imageSlider = $el->images->map(static fn(Attachment $slide) => [
                        'id' => $slide->getKey(),
                        'code' => $code,
                        'url' => $slide->url(),
                        'alt' => $slide->alt,
                        'description' => $slide->description,
                        'picture' => $hasPictureModule
                            ? Picture::init(new ImageFile($slide))
                                ->setLazyLoad(true)
                                ->setBreakpoints([1280 => 720, 960 => 1066, 670 => 800, 320 => 632])
                                ->setImageAttribute('alt', $slide->alt ?? '')
                                ->render()
                            : null
                    ]);
            }

            if (!$hasPictureModule) {
                if ($callback) {
                    $el = call_user_func($callback, $el);
                }

                return;
            }

            if ($el->relationLoaded('previewImage') && $el->previewImage->exists) {
                $el->previewImagePicture = Picture::init(new ImageFile($el->previewImage))
                    ->setLazyLoad(true)
                    ->setBreakpoints([1280 => 361, 960 => 521, 670 => 391, 320 => 545])
                    ->setImageAttribute('alt', $previewImage->alt ?? '')
                    ->render();
            }

            if ($el->relationLoaded('bodyImage') && $el->bodyImage->exists) {
                $el->bodyImagePicture = Picture::init(new ImageFile($el->bodyImage))
                    ->setLazyLoad(true)
                    ->setBreakpoints([1920 => 2560, 1440 => 1919, 1280 => 1439, 960 => 1279, 670 => 959, 320 => 669])
                    ->setImageAttribute('alt', $el->bodyImage->alt ?? '')
                    ->render();
            }

            if ($callback) {
                $el = call_user_func($callback, $el);
            }
        });

        return $res;
    }

    private function getRouteNameByCategory(Collection $categoryToRoute, int|string $categoryId): ?string
    {
        if ($categoryToRoute->isEmpty()) {
            return null;
        }

        $cardRoute = $categoryToRoute->get($categoryId);
        $routeName = null;

        if (is_string($cardRoute)) {
            $routeName = $cardRoute;
        } elseif ($cardRoute instanceof Route) {
            $routeName = $cardRoute->getName();
        }

        return $routeName;
    }


    public function getMinCacheTime(int $timeA, int $timeB): int
    {
        $minValue = min($timeA, $timeB);

        return $minValue < 1 ? max($timeA, $timeB) : $minValue;
    }

    public function getCategoryCacheTag(int|string $id): ?string
    {
        return $this->getCacheTag($id, 'newsCategory');
    }

    public function getElementCacheTag(int|string $id): ?string
    {
        return $this->getCacheTag($id, 'newsElement');
    }

    private function getCacheTag(int|string $id, string $prefix): ?string
    {
        return $id ? $prefix . '_' . $id : null;
    }
}
