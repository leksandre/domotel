<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\EstateVisual\Http\Controllers;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kelnik\Estate\Database\Seeders\EstateSeeder;
use Kelnik\Estate\Repositories\Contracts\PremisesStatusRepository;
use Kelnik\EstateVisual\Database\Seeders\SelectorSeeder;
use Kelnik\EstateVisual\Http\Controllers\SelectorController;
use Kelnik\EstateVisual\Models\Config;
use Kelnik\EstateVisual\Models\Contracts\SearchConfig;
use Kelnik\EstateVisual\Repositories\Contracts\SelectorRepository;
use Kelnik\EstateVisual\Services\Contracts\SearchConfigFactory;
use Kelnik\Tests\Feature\Estate\EstatePremisesTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;

final class SelectorControllerTest extends TestCase
{
    use EstatePremisesTrait {
        EstatePremisesTrait::makeConfig as traitMakeConfig;
    }
    use RefreshDatabase;
    use SiteTrait;

    protected function makeConfig(): SearchConfig
    {
        return new Config([
            'types' => [$this->getLivingTypeId()],
            'statuses' => resolve(PremisesStatusRepository::class)
                ->getListWithCardAvailable()
                ->pluck('id')
                ->toArray(),
            'filters' => config('kelnik-estate-visual.filters'),
            'plural' => '',
            'popup' => null,
            'form' => []
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EstateSeeder::class);
        $this->seed(SelectorSeeder::class);
        $this->instance(
            SearchConfigFactory::class,
            Mockery::mock(SearchConfigFactory::class, function (MockInterface $mock) {
                $mock->shouldReceive('make')->andReturn($this->makeConfig());
            })
        );
        $this->initSite();
    }

    public function testSelectorResultShouldReturnErrorOnInvalidRequest()
    {
        $faker = Factory::create('ru');
        $selector = resolve(SelectorRepository::class)->getActiveFirst();
        $requestData = [
            SelectorController::PARAM_REQUEST_STEP => $faker->word()
        ];

        $response = $this->post(
            route(
                'kelnik.estateVisual.getData',
                ['id' => $selector->getKey(), 'cid' => rand(1, 10)],
            ),
            $requestData
        );

        $responseArr = json_decode($response->getContent(), true);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertHeader('content-type', 'application/json');
        $this->assertNotTrue($responseArr['success']);
    }

    public function testSelectorResultShouldReturnValidOnComplexRequest()
    {
        $selector = resolve(SelectorRepository::class)->getActiveFirst();
        $requestData = [
            SelectorController::PARAM_REQUEST_STEP => \Kelnik\EstateVisual\Models\Steps\Factory::STEP_COMPLEX
        ];

        $response = $this->post(
            route(
                'kelnik.estateVisual.getData',
                ['id' => $selector->getKey(), 'cid' => rand(1, 10)]
            ),
            $requestData
        );
        $responseArr = json_decode($response->getContent(), true);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);
    }
}
