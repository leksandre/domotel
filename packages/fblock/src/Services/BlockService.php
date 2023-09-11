<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Services;

use Closure;
use Illuminate\Support\Collection;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\FBlock\Models\FlatBlock;
use Kelnik\FBlock\Providers\FBlockServiceProvider;
use Kelnik\FBlock\Repositories\Contracts\BlockRepository;
use Kelnik\Image\ImageFile;
use Kelnik\Image\Picture;
use Orchid\Attachment\Models\Attachment;

final class BlockService implements Contracts\BlockService
{
    public function __construct(private readonly BlockRepository $repository, private readonly CoreService $coreService)
    {
    }

    public function getBlockList(): Collection
    {
        return $this->prepareElements($this->repository->getActiveList());
    }

    public function prepareElements(
        Collection $res,
        ?Closure $callback = null
    ): Collection {
        if ($res->isEmpty()) {
            return $res;
        }

        $hasPictureModule = $this->coreService->hasModule('image');

        $res->each(function (FlatBlock $el) use ($callback, $hasPictureModule) {
            if ($el->relationLoaded('images') && $el->images->isNotEmpty()) {
                $el->imageSlider = $el->images->map(static fn(Attachment $slide) => [
                        'id' => $slide->getKey(),
                        'url' => $slide->url(),
                        'alt' => $slide->alt,
                        'description' => $slide->description,
                        'picture' => $hasPictureModule && strtolower($slide->getMimeType()) !== 'image/svg+xml'
                            ? Picture::init(new ImageFile($slide))
                                ->setLazyLoad(true)
                                ->setBreakpoints([1440 => 475, 1280 => 428, 960 => 406, 670 => 304, 320 => 440])
                                ->setImageAttribute('alt', $slide->alt ?? '')
                                ->render()
                            : null
                    ]);
            }

            if ($callback) {
                $el = call_user_func($callback, $el);
            }
        });

        return $res;
    }

    public function getCacheTag(): ?string
    {
        return FBlockServiceProvider::MODULE_NAME;
    }
}
