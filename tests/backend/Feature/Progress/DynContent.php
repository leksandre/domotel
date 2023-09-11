<?php

declare(strict_types=1);

namespace Kelnik\Tests\Feature\Progress;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\Camera;
use Kelnik\Tests\TestFile;
use Orchid\Attachment\Models\Attachment;

trait DynContent
{
    public function addImagesToAlbum(Album &$album, int $cnt = 5): void
    {
        $images = new Collection();

        for ($i = 0; $i <= $cnt; $i++) {
            $images->add($this->createImage());
        }

        if ($images->count()) {
            $album->images()->syncWithoutDetaching($images->pluck('id')->toArray());
        }
    }

    public function addCoverToCamera(Camera &$camera): void
    {
        $camera->cover()->associate($this->createImage());
        $camera->push();
    }

    public function createImage(): Attachment
    {
        $uploaded = UploadedFile::fake();
        $img = $uploaded->image('image-' . microtime(true) . '.jpg');
        $img = new TestFile($img);
        $img->setStorage($this->storage);

        return $img->load();
    }
}
