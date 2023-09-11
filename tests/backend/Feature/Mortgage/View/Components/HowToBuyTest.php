<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Mortgage\View\Components;

use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\Estate\View\Components\PremisesCard\PremisesCardBufferDto;
use Kelnik\Form\Models\Form;
use Kelnik\Mortgage\Models\Bank;
use Kelnik\Mortgage\Repositories\Contracts\BankRepository;
use Kelnik\Mortgage\Services\Contracts\MortgageService;
use Kelnik\Mortgage\View\Components\HowToBuy\HowToBuy;
use Kelnik\Page\Repositories\Contracts\BladeComponentRepository;
use Kelnik\Page\Services\Contracts\PageComponentBuffer;
use Kelnik\Page\Services\Contracts\PageService;
use Kelnik\Tests\Feature\PageComponentTrait;
use Kelnik\Tests\Feature\SiteTrait;
use Kelnik\Tests\TestCase;
use Mockery;

final class HowToBuyTest extends TestCase
{
    use RefreshDatabase;
    use PageComponentTrait;
    use SiteTrait;

    private const ITEMS_MIN = 3;
    private const ITEMS_MAX = 15;
    private const PREMISES_CACHE_TAG = 'premisesCard_test_cache_tag';

    private Filesystem $storage;
    private MortgageService $mortgageService;
    private PageService $pageService;
    private PageComponentBuffer $bufferService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->mortgageService = resolve(MortgageService::class);
        $this->pageService = resolve(PageService::class);
        $this->bufferService = resolve(PageComponentBuffer::class);
        $this->initSite();
    }

    private function createBanks(): Collection
    {
        return Bank::factory()->count(rand(5, 10))->hasPrograms(5)->create();
    }

    private function createForm(): Form
    {
        return Form::factory()->createOne(['active' => true]);
    }

    public function testComponentExists()
    {
        /** @var BladeComponentRepository $componentRepository */
        $componentRepository = resolve(BladeComponentRepository::class);
        $components = $componentRepository->getAdminList()->keys()->toArray();

        $this->assertContains(HowToBuy::initDataProvider()->getComponentCode(), $components);
    }

    public function testComponentAddedToPage()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);

        $this->assertDatabaseHas(
            $pageComponent->getTable(),
            [
                'page_id' => $page->getKey(),
                'component' => $pageComponent->component
            ]
        );
    }

    public function testComponentReturnValidContentAlias()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);
        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => $faker->slug,
                'text' => $faker->unique()->realText()
            ]
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $viewComponent = resolve(PageService::class)->initViewComponent($page, $pageComponent);

        $this->assertTrue($viewComponent->getContentAlias() === $pageData['content']['alias']);
    }

    public function testBlockIsVisibleWithoutVariants()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);

        $faker = Factory::create(config('app.faker_locale'));

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'how-to-buy-1',
                'text' => $faker->unique()->realText()
            ],
            'template' => HowToBuy::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
    }

    public function testVariantsVisibleWithoutBanks()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);

        $faker = Factory::create(config('app.faker_locale'));

        $this->createBanks();
        $variants = [];
        $limit = rand(4, 10);

        for ($i = 0; $i < $limit; $i++) {
            $variants[] = [
                'active' => $i % 2 === 0,
                'showBanks' => false,
                'title' => $faker->sentences(3, true),
                'text' => $faker->unique()->realText(50, true)
            ];
        }

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'how-to-buy-2',
                'text' => $faker->unique()->realText(),
                'variants' => $variants
            ],
            'banks' => [
                'id' => [],
                'showRange' => $faker->randomElement([true, false])
            ],
            'template' => HowToBuy::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $activeVariant = $unActiveVariant = [];
        foreach ($variants as $el) {
            if (!$activeVariant && $el['active']) {
                $activeVariant = $el;
            }
            if (!$unActiveVariant && !$el['active']) {
                $unActiveVariant = $el;
            }
        }

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());
        $this->assertStringNotContainsString('<article class="bank-card">', $response->getContent());
        $this->assertStringContainsString($activeVariant['title'], $response->getContent());
        $this->assertStringNotContainsString($unActiveVariant['title'], $response->getContent());
    }

    public function testShouldShowedVariantsWithBanks()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);

        $faker = Factory::create(config('app.faker_locale'));

        $banks = $this->createBanks();
        $activeBanks = resolve(BankRepository::class)->getActiveWithPrograms($banks->pluck('id')->toArray());
        $variants = [];
        $limit = rand(4, 8);

        for ($i = 0; $i < $limit; $i++) {
            $variants[] = [
                'active' => $i % 2 === 0,
                'showBanks' => $faker->randomElement([
                    HowToBuy::BANKS_VIEW_OFF,
                    HowToBuy::BANKS_VIEW_LIST,
                    HowToBuy::BANKS_VIEW_CALC
                ]),
                'title' => $faker->sentences(3, true),
                'text' => $faker->unique()->realText(50, true)
            ];
        }

        $form = resolve(CoreService::class)->hasModule('form')
            ? $this->createForm()
            : null;

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'how-to-buy-3',
                'text' => $faker->unique()->realText(),
                'factoidText' => $faker->unique()->realText(50),
                'button' => [
                    'active' => $faker->randomElement([true, false]),
                    'text' => $faker->unique()->sentence(2),
                    'form_id' => $form?->getKey() ?? 0
                ],
                'variants' => $variants
            ],
            'banks' => [
                'id' => array_values(
                    $banks->filter(static fn(Bank $el) => $el->active)->slice(0, 3)->pluck('id')->toArray()
                ),
                'showRange' => $faker->randomElement([true, false])
            ],
            'calc' => [
                'phone' => null,
                'schedule' => null,
                'text' => null,
                'helpText' => null,
                'buttons' => [
                    "consult" => [
                        'text' => null,
                        'form_id' => $form?->getKey() ?? 0
                    ]
                ]
            ],
            'template' => HowToBuy::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $activeVariant = $unActiveVariant = [];
        $banksIsVisible = false;

        foreach ($variants as $el) {
            if ($el['active'] && $el['showBanks'] !== HowToBuy::BANKS_VIEW_OFF) {
                $banksIsVisible = true;
            }
            if (!$activeVariant && $el['active']) {
                $activeVariant = $el;
            }
            if (!$unActiveVariant && !$el['active']) {
                $unActiveVariant = $el;
            }
        }

        $banksIsVisible = $banksIsVisible && $activeBanks->count();

        $anyActiveBank = $activeBanks->filter(
            static fn(Bank $el) => $el->active && in_array($el->id, $pageData['banks']['id'])
        )->first();

        $this->bufferService->reset();
        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['content']['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['title'], $response->getContent());
        $this->assertStringContainsString($pageData['content']['text'], $response->getContent());

        $this->assertStringContainsString($pageData['content']['factoidText'], $response->getContent());

        $this->assertStringContainsString($activeVariant['title'], $response->getContent());
        $this->assertStringNotContainsString($unActiveVariant['title'], $response->getContent());
        $this->assertTrue(
            ($banksIsVisible && str_contains($response->getContent(), $anyActiveBank?->title ?? ''))
            ||
            (!$banksIsVisible && !str_contains($response->getContent(), '<article class="bank-card">'))
        );
        $this->assertTrue(!$form || str_contains($response->getContent(), $form->title));
    }

    public function testShouldShowedVariantWithBanksByPremisesPrice()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);

        $faker = Factory::create(config('app.faker_locale'));

        $banks = $this->createBanks();
        $activeBanks = resolve(BankRepository::class)->getActiveWithPrograms($banks->pluck('id')->toArray());
        $variants = [
            [
                'active' => true,
                'showBanks' => HowToBuy::BANKS_VIEW_CALC,
                'title' => $faker->sentences(3, true),
                'text' => $faker->unique()->realText(50, true)
            ],
            [
                'active' => true,
                'showBanks' => HowToBuy::BANKS_VIEW_OFF,
                'title' => $faker->sentences(3, true),
                'text' => $faker->unique()->realText(50, true)
            ]
        ];

        $calcPhone = $faker->phoneNumber;
        $calcFormButton = $faker->sentence(1);
        $calcForm = resolve(CoreService::class)->hasModule('form')
            ? $this->createForm()
            : null;

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'how-to-buy-3',
                'text' => $faker->unique()->realText(),
                'factoidText' => $faker->unique()->realText(50),
                'button' => [
                    'active' => null,
                    'text' => null,
                    'form_id' => 0
                ],
                'variants' => $variants
            ],
            'banks' => [
                'id' => array_values(
                    $banks->filter(static fn(Bank $el) => $el->active)->slice(0, 3)->pluck('id')->toArray()
                ),
                'showRange' => false
            ],
            'calc' => [
                'card' => [
                    'meanTime' => 14
                ],
                'phone' => $calcPhone,
                'schedule' => null,
                'text' => null,
                'helpText' => null,
                'buttons' => [
                    'consult' => [
                        'text' => $calcFormButton,
                        'form_id' => $calcForm?->getKey() ?? 0
                    ]
                ]
            ],
            'template' => HowToBuy::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $this->bufferService->reset();
        $premisesBuff = new PremisesCardBufferDto();
        $premisesBuff->priceTotal = rand(1000, 6000) * 1000;
        $premisesBuff->cacheTags = [self::PREMISES_CACHE_TAG];

        $this->bufferService->add($premisesBuff);

        $response = $this->get($page->getUrl());
        $this->bufferService->reset();

        $response->assertOk();
        $this->assertStringContainsString((string)$premisesBuff->priceTotal, $response->getContent());
        $this->assertStringContainsString($calcPhone, $response->getContent());
        $this->assertStringContainsString($calcFormButton, $response->getContent());
    }

    public function testShouldShowedVariantWithoutBanks()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);

        $faker = Factory::create(config('app.faker_locale'));
        $limit = rand(4, 8);

        for ($i = 0; $i < $limit; $i++) {
            $variants[] = [
                'active' => $i % 2 === 0,
                'showBanks' => $faker->randomElement([
                    HowToBuy::BANKS_VIEW_OFF,
                    HowToBuy::BANKS_VIEW_LIST,
                    HowToBuy::BANKS_VIEW_CALC
                ]),
                'title' => $faker->sentences(3, true),
                'text' => $faker->unique()->realText(50, true)
            ];
        }

        $calcPhone = $faker->phoneNumber;
        $calcFormButton = $faker->sentence(1);
        $calcForm = resolve(CoreService::class)->hasModule('form')
            ? $this->createForm()
            : null;

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'how-to-buy-3',
                'text' => $faker->unique()->realText(),
                'factoidText' => $faker->unique()->realText(50),
                'button' => [
                    'active' => null,
                    'text' => null,
                    'form_id' => 0
                ],
                'variants' => $variants
            ],
            'banks' => [
                'id' => [],
                'showRange' => false
            ],
            'calc' => [
                'phone' => $calcPhone,
                'schedule' => null,
                'text' => null,
                'helpText' => null,
                'buttons' => [
                    "consult" => [
                        'text' => $calcFormButton,
                        'form_id' => $calcForm?->getKey() ?? 0
                    ]
                ]
            ],
            'template' => HowToBuy::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $this->bufferService->reset();
        $premisesBuff = new PremisesCardBufferDto();
        $premisesBuff->priceTotal = rand(1000, 6000) * 1000;
        $premisesBuff->cacheTags = [self::PREMISES_CACHE_TAG];

        $this->bufferService->add($premisesBuff);

        $response = $this->get($page->getUrl());
        $this->bufferService->reset();

        $response->assertOk();
        $this->assertStringNotContainsString((string)$premisesBuff->priceTotal, $response->getContent());
        $this->assertStringNotContainsString($calcPhone, $response->getContent());
        $this->assertStringNotContainsString($calcFormButton, $response->getContent());
    }

    public function testShouldShowedVariantsWithBanksUsingCache()
    {
        $page = $this->createPage();
        $pageComponent = $this->addComponentToPage($page, HowToBuy::class);

        $faker = Factory::create(config('app.faker_locale'));

        $banks = $this->createBanks();
        $variants = [];
        $limit = rand(4, 8);

        for ($i = 0; $i < $limit; $i++) {
            $variants[] = [
                'active' => true,
                'showBanks' => $faker->randomElement([true, false]),
                'title' => $faker->sentences(3, true),
                'text' => $faker->unique()->realText(50, true)
            ];
        }

        $pageData = [
            'content' => [
                'title' => $faker->sentence(3),
                'alias' => 'how-to-buy-3',
                'text' => $faker->unique()->realText(),
                'factoidText' => $faker->unique()->realText(50),
                'button' => [],
                'variants' => $variants
            ],
            'banks' => [
                'id' => array_values(
                    $banks->filter(static fn(Bank $el) => $el->active)->slice(0, 3)->pluck('id')->toArray()
                ),
                'showRange' => $faker->randomElement([true, false])
            ],
            'template' => HowToBuy::getTemplates()->last()->name
        ];

        $pageComponent->data->setValue(collect($pageData));
        $pageComponent->save();

        $activeVariant = $variants[array_rand($variants, 1)];

        $pageData['content']['showRange'] = $pageData['banks']['showRange'];
        $pageData['content']['banks'] = $this->mortgageService->getBanksListWithSummary($pageData['banks']['id'] ?? []);
        $pageData['content']['template'] = $pageData['template'];
        $pageData = $pageData['content'];

        $cacheId = resolve(PageService::class)
            ->getPageComponentCacheTag($page->getKey() . '_' . $pageComponent->getKey());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')->with($cacheId)->andReturn($pageData);
        Cache::swap($partialCacheMock);

        $response = $this->get($page->getUrl());

        $response->assertOk();
        $this->assertStringContainsString($pageData['alias'], $response->getContent());
        $this->assertStringContainsString($pageData['title'], $response->getContent());
        $this->assertStringContainsString($pageData['text'], $response->getContent());
        $this->assertStringContainsString($pageData['factoidText'], $response->getContent());
        $this->assertStringContainsString($activeVariant['title'], $response->getContent());
    }
}
