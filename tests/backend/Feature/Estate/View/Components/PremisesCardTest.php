<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Estate\View\Components;

use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\Database\Seeders\EstateSeeder;
use Kelnik\Estate\Models\Premises;
use Kelnik\Estate\Providers\EstateServiceProvider;
use Kelnik\Estate\Repositories\Contracts\FloorRepository;
use Kelnik\Estate\Repositories\Contracts\PremisesRepository;
use Kelnik\Estate\Services\Contracts\EstateService;
use Kelnik\Estate\View\Components\PremisesCard\DataProvider;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCard;
use Kelnik\Estate\View\Components\PremisesCard\RouteProvider;
use Kelnik\Form\Models\Form;
use Kelnik\Page\Models\PageComponentRoute;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Pdf\Services\Contracts\PdfService;
use Kelnik\Pdf\Services\PdfFileResponse;
use Kelnik\Tests\Feature\Estate\View\Components\Traits\EstatePageComponentTrait;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;

final class PremisesCardTest extends TestCase
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

    private function getInactiveFlat(): ?Premises
    {
        $flat = Premises::whereHas(
            'type',
            static fn(Builder $builder) => $builder->select('id')->whereHas(
                'typeGroup',
                static fn(Builder $b) => $b->select('id')->where('living', true)
            )
        )->whereHas(
            'status',
            static fn(Builder $builder) => $builder->select('id')->where('premises_card_available', true)
        )->with(['type.typeGroup'])
            ->orderBy('active', 'desc')
            ->first();

        if ($flat->active) {
            $flat->active = false;
            resolve(PremisesRepository::class)->save($flat);
        }

        return $flat;
    }

    private function getCompletelyInactiveFlat(): ?Premises
    {
        $flat = $this->getActiveFlat();
        $flat->floor->active = false;
        resolve(FloorRepository::class)->save($flat->floor);

        return $flat;
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

        $this->assertContains(PremisesCard::initDataProvider()->getComponentCode(), $components);
    }

    public function testActiveFlatAreShowed()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];
        $cacheId = $this->estateService->getPremisesCacheTag(
            'page_' . $route->page->getKey() .
            '_card_' . md5((string)$flat->getKey())
        );

        $flat = $this->estateService->preparePremises(
            new Collection([$flat]),
            new Collection([$flat->type->typeGroup->getKey() => $cardRouteName])
        )->first();

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $title = $flat->typeShortTitle ?? $flat->title;

        $response = $this->get($flat->url, ['Referer' => $flat->url]);
        $html = $response->getContent();
        $hasPriceOrTextOfBooked = !$flat->status->price_is_visible
            || str_contains($html, number_format($flat->price_total, 0, '.', ' '));

        $response->assertOk();
        $this->assertStringContainsString("<div class=\"flat__title\">{$title}</div>", $html);
        $this->assertStringContainsString("<title>{$title}</title>", $html);
        $this->assertTrue($hasPriceOrTextOfBooked);
        $this->assertTrue(Route::getRoutes()->hasNamedRoute($cardRouteName));
        $this->assertTrue(Cache::tags([
            $this->pageService->getPageComponentCacheTag($route->pageComponent->getKey()),
            $this->pageService->getDynComponentCacheTag($cardRouteName),
            $this->estateService->getModuleCacheTag(),
            $this->estateService->getPremisesCacheTag($flat->getKey())
        ])->has($cacheId));
    }

    public function testActiveFlatWithGalleryAreShowed()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        // Add gallery
        $uploaded = UploadedFile::fake();
        for ($i = 1; $i <= 2; $i++) {
            $img = $uploaded->image('flat-' . $flat->getKey() . '-' . $i . '.jpg', 800, 600);
            $img = new TestFile($img);
            $img->setStorage($this->storage);
            $img = $img->load();
            $flat->gallery()->attach($img->getKey());
        }
        $flat->load('gallery');

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);

        $response = $this->get($flat->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString($flat->gallery->first()->url(), $html);
    }

    public function testActiveFlatWithPopAreShowed()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];
        $faker = Factory::create(config('app.faker_locale'));

        $flat->planoplan_code = $faker->md5();
        resolve(PremisesRepository::class)->saveQuietly($flat);
        unset($faker);

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);

        $response = $this->get($flat->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('data-planoplan="', $html);
    }

    public function testActiveFlatShowedWithForm()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         * @var Form $form
         */
        $form = Form::factory()->createOne(['active' => true]);
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);
        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $component = $route->pageComponent->refresh();
        $buttonText = 'form_button_' . $component->getKey();
        $component->data->put('callbackButton', ['text' => $buttonText, 'form_id' => $form->getKey()]);
        $component->save();

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);

        $response = $this->get($flat->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('button j-popup-callback', $html);
        $this->assertStringContainsString($buttonText, $html);
    }

    public function testActiveFlatShowedWithCustomMetaTags()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);
        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $component = $route->pageComponent->refresh();
        $replacement = array_chunk($component->component::initDataProvider()->getReplacementFields(), 3);

        $metaTmpl = $metaData = [
            'title' => [],
            'description' => [],
            'keywords' => []
        ];

        $i = 0;
        foreach ($metaTmpl as $k => $v) {
            $tmp = $replacement[$i] ?? [];
            foreach ($tmp as $field) {
                $metaTmpl[$k][] = $field['var'];
                $val = Arr::get($flat, $field['src']);

                if (!empty($field['callback']) && is_callable($field['callback'])) {
                    $val = call_user_func($field['callback'], $val);
                }

                $metaData[$k][] = $val;
            }
            $i++;
            $metaTmpl[$k] = implode('|', $metaTmpl[$k]);
            $metaData[$k] = implode('|', $metaData[$k]);
        }

        $component->data->put('meta', $metaTmpl);
        $component->save();

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);

        $response = $this->get($flat->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString('<title>' . $metaData['title'] . '</title>', $html);
        $this->assertStringContainsString('<meta property="og:title" content="' . $metaData['title'] . '">', $html);
        $this->assertStringContainsString(
            '<meta name="description" content="' . $metaData['description'] . '">',
            $html
        );
        $this->assertStringContainsString(
            '<meta property="og:description" content="' . $metaData['description'] . '">',
            $html
        );
        $this->assertStringContainsString('<meta name="keywords" content="' . $metaData['keywords'] . '">', $html);
    }

    public function testActiveFlatShowedAsPdf()
    {
        if (!resolve(CoreService::class)->hasModule('pdf')) {
            $this->markTestSkipped('PDF module not found');
        }

        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);
        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];
        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $pdfUrl = \route(
            $cardRouteName,
            [
                'id' => $flat->getKey(),
                RouteProvider::PARAM_PRINT => RouteProvider::PRINT_TYPE_PDF
            ]
        );

        $storage = Storage::fake(null, config('kelnik-pdf.storage.config'));
        $pdfFile = UploadedFile::fake()->create('some-premises.pdf');

        $filePath = EstateServiceProvider::MODULE_NAME . '/' . $flat->getKey() . '.pdf';
        $storage->put($filePath, $pdfFile->getContent());

        $this->mock(PdfService::class, function (Mockery\MockInterface $mock) use ($filePath, $storage) {
            $mock->shouldReceive('getFileByPath')->andReturnNull();
            $mock->shouldReceive('printToFile')->andReturn(new PdfFileResponse($filePath, $storage));
        });

        $response = $this->get($pdfUrl);
        $response->assertDownload();
    }

    public function testInactiveFlatShouldReturnError()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getInactiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $response = $this->get($flat->url);

        $response->assertNotFound();
    }

    public function testCompletelyInactiveFlatShouldReturnError()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getCompletelyInactiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $response = $this->get($flat->url);

        $response->assertNotFound();
    }

    public function testShouldReturnErrorWhenRouteIsNotExists()
    {
        /**
         * @var Premises $flat
         * @var PageComponentRoute $route
         * @var string $cardRouteName
         */
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $route->delete();
        $this->app['router']->getRoutes()->refreshNameLookups();
        $response = $this->get($flat->url);

        $response->assertNotFound();
    }

    public function testActiveFlatAreShowedUsingCache()
    {
        $flat = $this->getActiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $flat = $this->estateService->preparePremises(
            new Collection([$flat]),
            new Collection([$flat->type->typeGroup->getKey() => $cardRouteName])
        )->first();

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $title = $flat->typeShortTitle ?? $flat->title;
        $hasFloor = $flat->relationLoaded('floor') && $flat->floor->exists;
        $hasBuilding = $hasFloor && $flat->floor->relationLoaded('building')
            && $flat->floor->building->exists;

        $cacheId = $this->estateService->getPremisesCacheTag(
            'page_' . $route->pageComponent->page_id .
            '_card_' . md5((string)$flat->getKey())
        );

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock
            ->shouldReceive('get')
            ->with($cacheId)
            ->andReturn([
                'element' => $flat,
                'background' => PremisesCard::BACKGROUND_COLORLESS,
                'currentLink' => url()->current(),
                'backLink' => '/',
                'template' => PremisesCard::getTemplates()->first()->name,
                'hasFloor' => $hasFloor,
                'hasBuilding' => $hasBuilding,
                'hasSection' => $hasFloor && $flat->relationLoaded('section') && $flat->section->exists,

                'hasPop' => mb_strlen($flat->planoplan_code ?? '') > 0,
                'hasPlan' => $flat->relationLoaded('imagePlan') && $flat->imagePlan,
                'has3dPlan' =>  $flat->relationLoaded('image3D') && $flat->image3D,
                'hasGallery' => $flat->relationLoaded('gallery') && $flat->gallery->isNotEmpty(),
                'hasFloorPlan' => $flat->relationLoaded('imageOnFloor') && $flat->imageOnFloor,
                'hasBuildingPlan' => $hasBuilding
                    && $flat->floor->building->relationLoaded('complexPlan')
                    && $flat->floor->building->complexPlan,
                'hasCompletion' => $hasBuilding && $flat->floor->building->relationLoaded('completion')
                    && $flat->floor->building->completion->exists
            ]);
        Cache::swap($partialCacheMock);

        $response = $this->get($flat->url);
        $html = $response->getContent();

        $response->assertOk();
        $this->assertStringContainsString("<div class=\"flat__title\">{$title}</div>", $html);
        $this->assertStringContainsString("<title>{$title}</title>", $html);
    }

    public function testInactiveFlatAreNotShowedUsingCache()
    {
        $flat = $this->getInactiveFlat();
        ['route' => $route, 'name' => $cardRouteName] = $this->createCardPage($flat->type->typeGroup);

        $this->app['router']->getRoutes()->refreshNameLookups();
        $routeParams = $this->app['router']->getRoutes()->getByName($cardRouteName)?->parameterNames() ?? [];

        $flat = $this->estateService->preparePremises(
            new Collection([$flat]),
            new Collection([$flat->type->typeGroup->getKey() => $cardRouteName])
        )->first();

        $flat->url = $this->setElementUrl($flat, $routeParams, $cardRouteName);
        $cacheId = $this->estateService->getPremisesCacheTag(
            'page_' . $route->pageComponent->page_id .
            '_card_' . md5((string)$flat->getKey())
        );

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn([]);
        Cache::swap($partialCacheMock);

        $response = $this->get($flat->url);

        $response->assertNotFound();
    }
}
