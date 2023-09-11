<?php

declare(strict_types=1);

namespace Kelnik\Progress\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;
use Kelnik\Core\Services\Video\Factory;
use Kelnik\Progress\Models\Album;
use Kelnik\Progress\Models\AlbumVideo;

final class AlbumPopupResource extends JsonResource
{
    /** @var Album */
    public $resource;

    public function toArray(Request $request): array|JsonSerializable|Arrayable
    {
        $videos = [];

        if ($this->resource->videos->isNotEmpty()) {
            $this->resource->videos->each(function (AlbumVideo $video) use (&$videos) {
                $videos[] = [
                    'thumb' => Factory::make($video->url)?->getThumb(),
                    'url' => $video->url
                ];
            });
        }

        return [
            'id' => $this->resource->getKey(),
            'title' => $this->resource->title,
            'comment' => $this->resource->comment,
            'description' => $this->resource->description,
            'videos' => $videos,
            'images' => $this->resource->images->pluck('url')
        ];
    }
}
