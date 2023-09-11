<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Progress\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Kelnik\Core\Services\Video\Factory;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\AlbumVideo;
use Kelnik\Progress\Models\Camera;
use Kelnik\Tests\Feature\Progress\DynContent;
use Kelnik\Tests\TestCase;

final class ProgressControllerTest extends TestCase
{
    use DynContent;
    use RefreshDatabase;

    private Filesystem $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = Storage::fake('public', ['url' => config('filesystems.disks.public.url')]);
    }

    public function testRouteCamerasReturnCorrectResponse()
    {
        // Dynamic content
        $cameras = Camera::factory()->count(10)->create();
        $inactiveCameras = $cameras->filter(static fn(Camera $camera) => !$camera->active);
        $activeCameras = $cameras->filter(static fn(Camera $camera) => $camera->active);

        if (!$activeCameras->count()) {
            $activeCameras->add(Camera::factory()->createOne(['active' => true]));
        }

        $randomInactive = $inactiveCameras->random(1)->first();
        $randomActive = $activeCameras->random(1)->first();
        $this->addCoverToCamera($randomActive);
        $activeFromDb = [
            'id' => $randomActive->id,
            'title' => $randomActive->title,
//            'description' => $randomActive->description,
            'video' => [
//                'thumb' => $randomActive->cover->url(),
                'url' => $randomActive->url
            ]
        ];

        // Asserts
        $response = $this->get(route('kelnik.progress.cameras'));
        $responseArr = json_decode($response->getContent(), true);
        $camerasFromResponse = new Collection($responseArr['data']['cameras'] ?? []);
        $activeFromResponse = $camerasFromResponse->first(
            static fn(array $el) => (int)$el['id'] === $activeFromDb['id']
        );

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);
        $this->assertNotEmpty($responseArr['data']['cameras']);
        $this->assertStringNotContainsString($randomInactive->title, $response->getContent());
        $this->assertStringContainsString($randomActive->title, $response->getContent());
        $this->assertStringContainsString($randomActive->title, $response->getContent());
        $this->assertEquals($activeFromResponse, $activeFromDb);
    }

    public function testRouteCamerasReturnEmptyListWhenAllCamerasIsInactive()
    {
        // Dynamic content
        $camera = Camera::factory()->createOne(['active' => false]);

        // Asserts
        $response = $this->get(route('kelnik.progress.cameras'));
        $responseArr = json_decode($response->getContent(), true);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);
        $this->assertEmpty($responseArr['data']['cameras']);
        $this->assertStringNotContainsString($camera->title, $response->getContent());
    }

    public function testRouteAlbumsReturnActiveAlbums()
    {
        // Dynamic content
        $albums = Album::factory()->count(10)->create();
        $albums->each(fn(Album $album) => $this->addImagesToAlbum($album));

        $inactiveAlbums = $albums->filter(static fn(Album $album) => !$album->active);
        $activeAlbums = $albums->filter(static fn(Album $album) => $album->active);

        if (!$activeAlbums->count()) {
            $activeAlbums->add(Album::factory()->createOne(['active' => true]));
        }

        if (!$inactiveAlbums->count()) {
            $inactiveAlbums->add(Album::factory()->createOne(['active' => false]));
        }

        $randomInactive = $inactiveAlbums->random(1)->first();
        $randomActive = $activeAlbums->random(1)->first();
        $activeFromDb = [
            'id' => $randomActive->getKey(),
            'title' => $randomActive->title,
            'comment' => $randomActive->comment,
            'description' => $randomActive->description,
            'videos' => [],
            'images' => $randomActive->images->pluck('url')->toArray()
        ];

        // Asserts
        $response = $this->get(route('kelnik.progress.albums'));
        $responseArr = json_decode($response->getContent(), true);
        $albumsFromResponse = new Collection($responseArr['data']['albums'] ?? []);
        $activeFromResponse = $albumsFromResponse->first(
            static fn(array $el) => (int)$el['id'] === $activeFromDb['id']
        );

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);
        $this->assertNotEmpty($responseArr['data']['albums']);
        $this->assertEquals($activeFromResponse, $activeFromDb);
    }

    public function testRouteAlbumsReturnEmptyListWhenAllAlbumsIsInactive()
    {
        // Dynamic content
        $album = Album::factory()->createOne(['active' => false]);
        $this->addImagesToAlbum($album);

        // Asserts
        $response = $this->get(route('kelnik.progress.albums'));
        $responseArr = json_decode($response->getContent(), true);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);
        $this->assertEmpty($responseArr['data']['albums']);
        $this->assertStringNotContainsString($album->title, $response->getContent());
    }

    public function testShouldReturnAlbumWithVideosOnly()
    {
        // Dynamic content
        $album = Album::factory()->hasVideos(3)->createOne(['active' => true]);
        $albumArr = [
            'id' => $album->getKey(),
            'title' => $album->title,
            'comment' => $album->comment,
            'description' => $album->description,
            'videos' => [],
            'images' => []
        ];

        $album->videos->each(function (AlbumVideo $albumVideo) use (&$albumArr) {
            $albumArr['videos'][] = [
                'thumb' => Factory::make($albumVideo->url)?->getThumb(),
                'url' => $albumVideo->url
            ];
        });

        // Asserts
        $response = $this->get(route('kelnik.progress.albums'));
        $responseArr = json_decode($response->getContent(), true);
        $albumsFromResponse = new Collection($responseArr['data']['albums'] ?? []);
        $activeFromResponse = $albumsFromResponse->first(static fn(array $el) => (int)$el['id'] === $albumArr['id']);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertTrue($responseArr['success']);
        $this->assertNotEmpty($responseArr['data']['albums']);
        $this->assertEquals($activeFromResponse, $albumArr);
    }
}
