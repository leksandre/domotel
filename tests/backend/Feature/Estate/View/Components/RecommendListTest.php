<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Estate\View\Components;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Estate\Database\Seeders\EstateSeeder;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Estate\View\Components\RecommendList\ListFilter;
use Kelnik\Estate\View\Components\RecommendList\RecommendList;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageComponentBuffer;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\Estate\View\Components\Traits\EstatePageComponentTrait;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class RecommendListTest extends TestCase
{
    use EstatePageComponentTrait;
    use PageComponentTrait;
    use RefreshDatabase;
    use SiteTrait;

    private Filesystem $storage;
    private EstateService $estateService;
    private PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->estateService = resolve(EstateService::class);
        $this->pageService = resolve(PageService::class);
        $this->seed(EstateSeeder::class);
        $this->initSite();
    }

    private function getActiveFlat(): ?Premises
    {
        return Premises::where('active', true)
            ->whereHas(
                'type',
                static fn(Builder $builder) => $builder->select('id')
                    ->whereHas(
                        'typeGroup',
                        static fn(Builder $b) => $b->select('id')->where('living', true)
                    )
            )
            ->whereHas(
                'status',
                static fn(Builder $builder) => $builder->select('id')->where('premises_card_available', true)
            )
            ->with(['type.typeGroup'])
            ->first();
    }

    private function setElementUrl(Premises $premises, array $routeParams, string $routeName): string
    {
        if (!$routeParams || !$routeName) {
            return '#';
        }

        $params = [];
        foreach ($routeParams as $paramName) {
            $params[$paramName] = $premises->{$paramName} ?? null;
        }

        return route($routeName, $params, false);
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(RecommendList::initDataProvider()->getComponentCode(), $components);
    }

    /** @throws Exception */
    public function testShowedWhenPageHasActivePremisesCard()
    {
        resolve(PageComponentBuffer::class)->reset();

        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $page = $route->pageComponent->page;
        $recommend = $this->addComponentToPage($page, RecommendList::class);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $flat = $this->estateService->preparePremises(
            new Collection([$flat]),
            new Collection([$flat->type->typeGroup->getKey() => $cardRouteName])
        )->first();

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $title = trans('kelnik-estate::front.components.recommendList.title');

        $response = $this->get($flat->url, ['Referer' => $flat->url]);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('<h2>' . $title . '</h2>', $html);
    }

    /** @throws Exception */
    public function testNotShowedWhenPageNotHasPremisesCard()
    {
        resolve(PageComponentBuffer::class)->reset();
        $page = $this->createPage();
        $recommend = $this->addComponentToPage($page, RecommendList::class);

        $title = trans('kelnik-estate::front.components.recommendList.title');

        $response = $this->get($page->getUrl());
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringNotContainsString('<h2>' . $title . '</h2>', $html);
    }

    /** @throws Exception */
    public function testShowedUsingCache()
    {
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $page = $route->pageComponent->page;
        $recommend = $this->addComponentToPage($page, RecommendList::class);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $flat = $this->estateService->preparePremises(
            new Collection([$flat]),
            new Collection([$flat->type->typeGroup->getKey() => $cardRouteName])
        )->first();

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $title = trans('kelnik-estate::front.components.recommendList.title');
        $filter = new ListFilter();
        $filter->typeGroupKey = (int)$flat->type->group_id;
        $filter->typeKey = (int)$flat->type_id;
        $filter->excludeKey = $flat->getKey();
        $filter->floorNum = (int)$flat->floor->number;
        $filter->priceTotal = $flat->price_total;
        $filter->areaTotal = $flat->area_total;
        $filter->limit = RecommendList::COUNT_DEFAULT;

        /** @var Collection $elements */
        $elements = resolve(PremisesRepository::class)->getRecommends($filter);
        $randomEl = $elements->random(1)?->first();
        unset($filter);

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')
            ->withArgs(static fn($cacheId) => stripos($cacheId, 'recommend') !== false)
            ->andReturn([
                'title' => $title,
                'count' => RecommendList::COUNT_DEFAULT,
                'template' => RecommendList::getTemplates()->first()?->name,
                'list' => $elements
            ]);
        Cache::swap($partialCacheMock);

        $response = $this->get($flat->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('<h2>' . $title . '</h2>', $html);
        $this->assertStringContainsString($randomEl->title, $html);
    }
}
