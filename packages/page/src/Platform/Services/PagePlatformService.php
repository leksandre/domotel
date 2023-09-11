<?php

declare(strict_types=1);

namespace Kelnik\Page\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Core\Services\Contracts\SettingsService;
use Kelnik\Core\Theme\Contracts\Color;
use Kelnik\Page\Models\Enums\RedirectType;
use Kelnik\Page\Models\Page;
use Kelnik\Page\Models\PageComponent;
use Kelnik\Page\Platform\Exceptions\PageNotFound;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageComponentRepository;
use Kelnik\Page\Repositories\Contracts\PageRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageDynamicComponent;
use Orchid\Support\Facades\Toast;

final class PagePlatformService implements Contracts\PagePlatformService
{
    public function __construct(
        private PageRepository $repository,
        private CoreService $coreService,
        private PageService $pageService
    ) {
    }

    public function save(int|string $siteId, int|string $pageId, array $data): RedirectResponse
    {
        $page = $pageId
            ? $this->repository->findByPrimary($pageId)
            : new Page();

        if (($pageId && !$page->exists) || ($page->exists && $siteId !== $page->site_id)) {
            throw new PageNotFound();
        }

        $pageData = Arr::only($data, [
            'title',
            'slug',
            'active',
            'redirect_url',
            'css_classes'
        ]);

        $pageData['redirect_type'] = RedirectType::tryFrom(
            (int)Arr::get($data, 'redirect_type_')
        )?->value ?? RedirectType::Disabled->value;

        $formSlug = Arr::get($pageData, 'slug');

        if (($page->exists && $page->slug !== $formSlug) || !$page->exists && $formSlug) {
            $validator = Validator::make(
                $pageData,
                [
                    'slug' => 'nullable|max:255|regex:/^[a-z0-9\-_.]+$/i',
                    'css_classes' => 'nullable|max:255'
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        if (!$page->type->isSimple()) {
            unset($pageData['slug'], $pageData['redirect_url']);
        }

        $page->fill($pageData);

        if (!$page->exists) {
            $page->site_id = $siteId;
        }

        $page->meta->fill(Arr::get($data, 'meta', []));

        if (!$this->repository->pageIsUnique($page)) {
            return back()->withErrors(
                new MessageBag([
                   trans('validation.unique', ['attribute' => trans('kelnik-page::admin.slug')])
                ])
            )->withInput();
        }

        $this->repository->save($page);
        Toast::info(trans('kelnik-page::admin.saved'));

        return redirect()->route(
            $this->coreService->getFullRouteName('page.list'),
            ['site' => $siteId]
        );
    }

    /** @inheritdoc */
    public function delete(int|string $siteId, int|string $pageId): RedirectResponse
    {
        $page = $this->repository->findByPrimary($pageId);

        if (($pageId && !$page->exists) || ($page->exists && $siteId !== $page->site_id)) {
            throw new PageNotFound();
        }

        if (!$page->type->isSimple()) {
            Toast::error(trans('kelnik-page::admin.errors.isNotSimplePage'));

            return redirect()->back();
        }

        $siteId = $page->site_id;

        $this->repository->delete($page)
            ? Toast::info(trans('kelnik-page::admin.deleted'))
            : Toast::warning('An error has occurred');

        return redirect()->route(
            $this->coreService->getFullRouteName('page.list'),
            ['site' => $siteId]
        );
    }

    public function addComponent(int|string $siteId, int|string $pageId, int|string $componentId): bool
    {
        $page = $this->repository->findByPrimary($pageId);
        $componentName = resolve(BladeComponentRepository::class)->findByPrimary($componentId);

        if (!$page->exists || $page->site_id !== $siteId || !$componentName) {
            throw new InvalidArgumentException(trans('kelnik-page::admin.errors.componentNotFound'));
        }

        if (
            is_a($componentName, KelnikPageDynamicComponent::class, true)
            && $page->components?->first(static fn(PageComponent $el) => $el->isDynamic())
        ) {
            throw new InvalidArgumentException(trans('kelnik-page::admin.errors.dynComponentLimit'));
        }

        $pageComponent = new PageComponent([
            'component' => $componentName,
            'priority' => ($page->components()->max('priority') ?? PageComponent::PRIORITY_DEFAULT) + 1
        ]);

        $pageComponent->data->setDefaultValue();

        return $this->repository->addComponent($page, $pageComponent);
    }

    public function sortComponents(int|string $siteId, int|string $pageId, array $componentsPriority): bool
    {
        /** @var PageComponentRepository $componentRepo */
        $componentRepo = resolve(PageComponentRepository::class);
        $components = $componentRepo->getComponentsByPage($siteId, $pageId);

        if ($components->isEmpty()) {
            return false;
        }

        $components->each(static function (PageComponent $el) use ($componentsPriority, $componentRepo) {
            if (in_array($el->getKey(), $componentsPriority)) {
                $el->priority = (int)array_search($el->getKey(), $componentsPriority) + PageComponent::PRIORITY_DEFAULT;
                $componentRepo->save($el);
            }
        });

        return true;
    }

    public function createSlugByTitle(string $title): string
    {
        return Str::slug($title);
    }

    public function isUnique(int|string $siteId, int|string $pageId, ?string $slug = null): bool
    {
        $page = new Page([
            'site_id' => $siteId,
            'slug' => $slug
        ]);
        $page->id = $pageId;
        $page->exists = true;

        return resolve(PageRepository::class)->pageIsUnique($page);
    }

    public function prepareComponentColorsFromRequest(Collection $colors, array $requestColorValues): Collection
    {
        $colorKeys = [];
        $colors = $colors->map(static function (Color $color) use (&$colorKeys, $requestColorValues) {
            if (isset($requestColorValues[$color->getFullName()])) {
                $color->setValue($requestColorValues[$color->getFullName()]);
            }

            $colorKeys[] = $color->getName();

            return $color;
        });

        /** @var SettingsService $settingsService */
        $settingsService = resolve(SettingsService::class);
        $defColors = $settingsService->getCurrentColors($colorKeys);
        $defColors = collect($defColors);

        return $settingsService->prepareColors($colors, $defColors);
    }

    public function getBreadcrumbs(Page $page, int $level = 0): array
    {
        $res = [];

        if (!$level) {
            $res[] = [trans('kelnik-page::front.homePage'), '/'];
        }

        if ($page->hasParent()) {
            $parent = $page->parent;
            $res[] = [$parent->title, $parent->getUrl()];

            if ($parent->hasParent()) {
                $res = array_merge($res, $this->getBreadcrumbs($parent, $level + 1));
            }
        }

        return $res;
    }

    public function getPageUrlById(string|int $primary): string
    {
        if (!$primary) {
            return '';
        }

        $page = $this->repository->findByPrimary($primary);

        return $page->exists && $page->active
            ? route($this->pageService->getPageRouteName($page), [], false)
            : '';
    }
}
