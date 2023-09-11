<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateSearch\Http\Controllers;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Estate\Database\Seeders\EstateSeeder;
use Kelnik\EstateSearch\Http\Controllers\SearchController;
use Kelnik\EstateSearch\Http\Resources\PremisesResource;
use Kelnik\EstateSearch\Models\Enums\PaginationType;
use Kelnik\EstateSearch\Models\Filters\Type;
use Kelnik\EstateSearch\Services\Contracts\SearchConfigFactory;
use Kelnik\EstateSearch\Services\Contracts\SearchService;
use Kelnik\EstateSearch\View\Components\Search\Search;
use Kelnik\Tests\Feature\Estate\EstatePremisesTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;

final class SearchControllerTest extends TestCase
{
    use EstatePremisesTrait;
    use RefreshDatabase;
    use SiteTrait;

    protected ?SearchService $searchService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EstateSeeder::class);
        $this->initSite();
        $this->searchService = resolve(SearchService::class, ['config' => $this->makeConfig()]);
        $this->instance(
            SearchConfigFactory::class,
            Mockery::mock(SearchConfigFactory::class, function (MockInterface $mock) {
                $mock->shouldReceive('make')->andReturn($this->makeConfig());
            })
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->searchService = null;
    }

    public function testSearchResultShouldReturnErrorOnInvalidRequest()
    {
        $faker = Factory::create('ru');
        $requestData = [
            SearchController::PARAM_REQUEST_TYPE => $faker->word()
        ];
        $response = $this->post(route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]), $requestData);

        $responseArr = json_decode($response->getContent(), true);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue(!$responseArr['success']);
    }

    public function testSearchResultShouldReturnValidDataOnInitRequest()
    {
        $requestData = [
            SearchController::PARAM_REQUEST_TYPE => 'init'
        ];

        $request = Request::create(
            route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]),
            'POST',
            $requestData
        );
        $response = $this->post(route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]), $requestData);
        $responseArr = json_decode($response->getContent(), true);
        $res = $this->searchService->getAllResults($requestData);
        $flatsCount = $res->get('count');
        $firstFlat = $res->get('items')->first();
        unset($res);

        $config = $this->searchService->getConfig();

        // Asserts
        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);

        $this->assertNotEmpty($responseArr['data']['settings']);
        $this->assertNotEmpty($responseArr['data']['filters']);

        $this->assertTrue($responseArr['data']['settings']['view']['type'] === Search::VIEW_TYPE_CARD);
        $this->assertTrue($responseArr['data']['settings']['premisesToShow'] === $config->pagination->perPage);
        $this->assertTrue($responseArr['data']['settings']['groupOptions'] === 2);

        $this->assertTrue(count($responseArr['data']['premises']) === $flatsCount);
        $this->assertEquals(
            (new PremisesResource($firstFlat))->toArray($request),
            current($responseArr['data']['premises'])
        );
    }

    public function testSearchResultShouldReturnValidDataOnInitRequestWithPagination()
    {
        $config = $this->makeConfig(PaginationType::Backend);

        $this->instance(
            SearchConfigFactory::class,
            Mockery::mock(SearchConfigFactory::class, function (MockInterface $mock) use ($config) {
                $mock->shouldReceive('make')->andReturn($config);
            })
        );

        $requestData = [
            SearchController::PARAM_REQUEST_TYPE => 'init'
        ];

        $request = Request::create(
            route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]),
            'POST',
            $requestData
        );
        $response = $this->post(route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]), $requestData);
        $responseArr = json_decode($response->getContent(), true);
        $res = $this->searchService->getResults($requestData);
        $flatsCount = $res->get('items')->count();
        $firstFlat = $res->get('items')->first();
        unset($res);

        // Asserts
        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);

        $this->assertNotEmpty($responseArr['data']['settings']);
        $this->assertNotEmpty($responseArr['data']['filters']);

        $this->assertTrue($responseArr['data']['pagination']['limit'] === $flatsCount);
        $this->assertEquals(
            (new PremisesResource($firstFlat))->toArray($request),
            current($responseArr['data']['premises'])
        );
    }

    public function testSearchResultShouldReturnValidDataOnResetRequest()
    {
        $requestData = [
            SearchController::PARAM_REQUEST_TYPE => 'reset'
        ];

        $response = $this->post(route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]), $requestData);
        $responseArr = json_decode($response->getContent(), true);

        // Asserts
        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');

        $this->assertTrue($responseArr['success']);
        $this->assertNotEmpty($responseArr['data']['settings']);
        $this->assertNotEmpty($responseArr['data']['filters']);
        $this->assertNotEmpty($responseArr['data']['premises']);
    }

    public function testSearchResultShouldReturnValidDataOnFilterRequest()
    {
        $faker = Factory::create('ru');
        $requestData = [
            SearchController::PARAM_REQUEST_TYPE => 'init'
        ];
        $form = $this->searchService->getForm($requestData);
        $flatsCountOnInit = $form->get('count');
        $baseBorders = $form->get('baseBorders');
        $filter = $baseBorders->first(static fn ($el) => $el['name'] === Type::NAME)?->toArray() ?? [];
        $filterValue = Arr::get($faker->randomElement($filter['values'] ?? []), 'id', 0);

        $requestData[SearchController::PARAM_REQUEST_TYPE] = 'filter';
        $requestData[$filter['name']][$filterValue] = $filterValue;

        $request = Request::create(
            route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]),
            'POST',
            $requestData
        );
        $response = $this->post(route('kelnik.estateSearch.results', ['cid' => rand(1, 10)]), $requestData);
        $responseArr = json_decode($response->getContent(), true);
        $res = $this->searchService->getAllResults($requestData);
        $flatsCount = $res->get('count');
        $firstFlat = $res->get('items')->first();
        unset($res);

        $config = $this->searchService->getConfig();

        // Asserts
        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);

        $this->assertNotEmpty($responseArr['data']['settings']);
        $this->assertNotEmpty($responseArr['data']['filters']);

        $this->assertTrue($responseArr['data']['settings']['view']['type'] === Search::VIEW_TYPE_CARD);
        $this->assertTrue($responseArr['data']['settings']['premisesToShow'] === $config->pagination->perPage);
        $this->assertTrue($responseArr['data']['settings']['groupOptions'] === 2);

        $this->assertTrue(count($responseArr['data']['premises']) === $flatsCount);
        $this->assertTrue($flatsCountOnInit !== $flatsCount);
        $this->assertEquals(
            (new PremisesResource($firstFlat))->toArray($request),
            current($responseArr['data']['premises'])
        );
    }
}
