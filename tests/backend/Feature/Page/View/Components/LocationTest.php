<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Page\View\Components;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Page\View\Components\Contracts\KelnikPageComponent;
use Kelnik\Page\View\Components\Location\Location;
use Kelnik\Tests\TestFile;
use Mockery;

final class LocationTest extends AbstractTestComponent
{
    use RefreshDatabase;

    protected KelnikPageComponent|string $componentNamespace = Location::class;

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => $faker->slug,
                'usp' => [],
                'map' => []
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    public function testComponentSvgReturnValidResultOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        resolve(SettingsPlatformService::class)->saveMap('core', ['service' => 'yandex']);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $uploaded = UploadedFile::fake();
        $iconJpg = $uploaded->image('icon.jpg', 40, 40);
        $iconJpg = new TestFile($iconJpg);
        $iconJpg->setStorage($this->storage);
        $iconJpg = $iconJpg->load();

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" ' .
            'xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 10 10">' .
            '<path id="3fqaa" d=""></path></svg>';
        $iconSvg = $uploaded->createWithContent('plan.svg', $svg);
        $iconSvg = new TestFile($iconSvg);
        $iconSvg->setStorage($this->storage);
        $iconSvg = $iconSvg->load();

        $usp = [
            [
                'title' => $faker->unique()->company,
                'icon' => $iconJpg->id,
                'sort' => 500
            ],
            [
                'title' => $faker->unique()->company,
                'icon' => $iconSvg->id,
                'sort' => 510
            ]
        ];

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => 'location',
                'usp' => $usp,
            ],
            'map' => [
                'center' => [
                    'lat' => $this->getLat($faker),
                    'lng' => $this->getLng($faker)
                ],
                'zoom' => rand(10, 16),
                'route' => [
                    'active' => true,
                    'title' => $faker->word,
                    'link' => $faker->url
                ],
                'markerTypes' => [
                    [
                        'icon' => $iconSvg->id,
                        'title' => 'Банки',
                        'code' => 'bank',
                        'sort' => 500
                    ],
                    [
                        'icon' => null,
                        'title' => 'Детские сады',
                        'code' => 'kindergarden',
                        'sort' => 510
                    ],
                    [
                        'icon' => $iconJpg->id,
                        'title' => 'Больницы',
                        'code' => 'hospital',
                        'sort' => 520
                    ]
                ],
                'markers' => [
                    [
                        'coords' => $this->getLat($faker) . ',' . $this->getLng($faker),
                        'type' => null,
                        'icon' => $iconSvg->id,
                        'image' => $iconJpg->id,
                        'title' => "ЖК Березка",
                        'description' => "описание"
                    ],
                    [
                        'coords' => $this->getLat($faker) . ',' . $this->getLng($faker),
                        'type' => 'bank',
                        'code' => '1165450400',
                        'icon' => null,
                        'image' => null,
                        'title' => 'Сбербанк России',
                        'description' => 'Прибрежная ул., 8, корп. 1, Санкт-Петербург, Россия'
                    ],
                    [
                        'coords' => $this->getLat($faker) . ',' . $this->getLng($faker),
                        'type' => 'kindergarden',
                        'code' => '1132486998',
                        'icon' => null,
                        'image' => null,
                        'title' => 'Детсад №1',
                        'description' => 'Верхняя ул., 5, корп. 1, д. Старая, Россия'
                    ],
                    [
                        'coords' => $this->getLat($faker) . ',' . $this->getLng($faker),
                        'type' => 'hospital',
                        'code' => '1132486900',
                        'icon' => null,
                        'image' => null,
                        'title' => 'Больница №1',
                        'description' => 'Адрес больницы'
                    ]
                ]
            ]
        ];
        unset($iconSvg);

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($iconJpg->name, $response->getContent());
        $this->assertStringContainsString($svg, $response->getContent());
        $this->assertStringContainsString($usp[0]['title'], $response->getContent());
        $this->assertStringContainsString($usp[1]['title'], $response->getContent());
        $this->assertStringContainsString(
            '<div class="yandex-map j-location-map" data-json="',
            $response->getContent()
        );
    }

    public function testComponentWithEmptyMarkerTypesAndMarkerList()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => 'location',
                'usp' => []
            ],
            'map' => [
                'center' => [
                    'lat' => $this->getLat($faker),
                    'lng' => $this->getLng($faker)
                ],
                'zoom' => rand(10, 16),
                'route' => [
                    'active' => true,
                    'title' => $faker->word,
                    'link' => $faker->url
                ],
                'markerTypes' => [],
                'markers' => []
            ]
        ];
        unset($icon);

        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
    }

    public function testComponentHasMarkersWithEmptyType()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => 'location',
                'usp' => []
            ],
            'map' => [
                'center' => [
                    'lat' => $this->getLat($faker),
                    'lng' => $this->getLng($faker)
                ],
                'zoom' => rand(10, 16),
                'route' => [
                    'active' => true,
                    'title' => $faker->word,
                    'link' => $faker->url
                ],
                'markerTypes' => [
                    [
                        'icon' => null,
                        'title' => 'Банки',
                        'code' => 'bank',
                        'sort' => 500
                    ],
                    [
                        'icon' => null,
                        'title' => 'Детские сады',
                        'code' => 'kindergarden',
                        'sort' => 510
                    ]
                ],
                'markers' => [
                    [
                        'coords' => $this->getLat($faker) . ',' . $this->getLng($faker),
                        'type' => null,
                        'icon' => null,
                        'image' => null,
                        'title' => "ЖК Березка",
                        'description' => "описание"
                    ]
                ]
            ]
        ];
        unset($icon);

        $pageData = collect($pageData);
        $pageComponent->data->setValue($pageData);
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
    }

    public function testComponentUseCacheOnPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page);

        gc_collect_cycles();
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->company,
                'alias' => 'location',
                'usp' => []
            ],
            'map' => [
                'center' => [
                    'lat' => $this->getLat($faker),
                    'lng' => $this->getLng($faker)
                ],
                'zoom' => rand(10, 16),
                'route' => [
                    'active' => true,
                    'title' => $faker->word,
                    'link' => $faker->url
                ],
                'markerTypes' => [],
                'markers' => []
            ]
        ];
        unset($icon);

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $pageData['content']['map'] = $pageData['map'];
        $pageData = $pageData['content'];
        $pageData = collect($pageData);

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData->get('title'), $response->getContent());
    }

    private function getLat(Generator $faker): float
    {
        return $faker->randomFloat(7, 58, 60);
    }

    private function getLng(Generator $faker): float
    {
        return $faker->randomFloat(7, 30, 31);
    }
}
