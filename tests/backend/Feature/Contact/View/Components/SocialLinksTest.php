<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Contact\View\Components;

use Faker\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kelnik\Contact\Models\SocialLink;
use Kelnik\Contact\Repositories\Contracts\SocialLinkRepository;
use Kelnik\Contact\Services\Contracts\ContactService;
use Kelnik\Contact\View\Components\Social\SocialLinks;
use Kelnik\Tests\TestCase;
use Kelnik\Tests\TestFile;
use Mockery;

final class SocialLinksTest extends TestCase
{
    use RefreshDatabase;

    private const ITEMS_MIN = 5;
    private const ITEMS_MAX = 8;

    private Filesystem $storage;
    private ContactService $contactService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
        $this->contactService = resolve(ContactService::class);
    }

    private function createElements(): Collection
    {
        $links = SocialLink::factory()->count(rand(self::ITEMS_MIN, self::ITEMS_MAX))->create();
        $activeElement = $links->first(static fn(SocialLink $el) => $el->isActive());
        $inActiveElement = $links->first(static fn(SocialLink $el) => !$el->isActive());
        $socRepo = resolve(SocialLinkRepository::class);

        if (!$activeElement) {
            $el = SocialLink::factory()->createOne(['active' => true]);
            $socRepo->save($el);
            $links->add($el);
        }

        if (!$inActiveElement) {
            $el = SocialLink::factory()->createOne(['active' => false]);
            $socRepo->save($el);
            $links->add($el);
        }

        $faker = Factory::create(config('app.faker_locale'));
        $uploaded = UploadedFile::fake();

        $img = $uploaded->image($faker->unique()->slug() . '.jpg', 100, 100);
        $img = new TestFile($img);
        $img->setStorage($this->storage);
        $img = $img->load();

        $svg = $uploaded->create($faker->unique()->slug() . '.svg', rand(1_000, 20_000), 'image/svg+xml');
        $svg = new TestFile($svg);
        $svg->setStorage($this->storage);
        $svg = $svg->load();

        return $links->each(static function (SocialLink $el) use ($img, $svg, &$socRepo) {
            if ($el->isActive()) {
                if ($img) {
                    $el->icon_id = $img->getKey();
                    $socRepo->save($el);
                    $img = false;
                } elseif ($svg) {
                    $el->icon_id = $svg->getKey();
                    $socRepo->save($el);
                    $svg = false;
                }
            }
        });
    }

    public function testActiveLinksShowedOnPage()
    {
        /** @var SocialLink $activeElement */
        $links = $this->createElements();
        $activeElement = $links->first(static fn(SocialLink $el) => $el->isActive());
        $inActiveElement = $links->first(static fn(SocialLink $el) => !$el->isActive());

        $component = new SocialLinks();
        $cacheId = $this->contactService->getSocialCacheTag();

        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString(e($activeElement->title), $html);
        $this->assertStringNotContainsString(e($inActiveElement->title), $html);
        $this->assertTrue(Cache::tags([
            $this->contactService->getSocialCacheTag()
        ])->has($cacheId));
    }

    public function testActiveLinksShowedOnPageUsingCache()
    {
        $elements = $this->createElements();
        $activeElement = $elements->first(static fn(SocialLink $el) => $el->isActive());
        $inActiveElement = $elements->first(static fn(SocialLink $el) => !$el->isActive());

        $partialCacheMock = Mockery::mock(Cache::driver())->makePartial();
        $partialCacheMock->shouldReceive('get')
            ->withArgs(static fn($param) => stripos($param, 'contact_social') !== false)
            ->andReturn([
                'list' => $elements->filter(static fn(SocialLink $el) => $el->isActive())
            ]);
        Cache::swap($partialCacheMock);

        $component = new SocialLinks();
        $html = $component->render()?->render() ?? '';

        $this->assertStringContainsString($activeElement->title, $html);
        $this->assertStringNotContainsString($inActiveElement->title, $html);
    }
}
