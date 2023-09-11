<?php

declare(strict_types=1);

namespace Kelnik\Progress\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Kelnik\Progress\Http\Resources\AlbumPopupResource;
use Kelnik\Progress\Repositories\Contracts\AlbumRepository;
use Kelnik\Progress\Services\Contracts\ProgressService;

final class AlbumsPopupController extends ProgressController
{
    public function __construct(
        private ProgressService $progressService,
        private AlbumRepository $albumRepository
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $group = $this->getGroupId();

        $cacheId = $this->progressService->getAlbumListCacheTag() . '_popup' . ($group ? '_' . $group : '');
        $res = Cache::get($cacheId);

        if ($res === null) {
            $res = AlbumPopupResource::collection($this->albumRepository->getActive(group: $group));
            $tags = [$this->progressService->getAlbumListCacheTag()];

            if ($group) {
                $tags[] = $this->progressService->getGroupCacheTag($group);
            }

            Cache::tags($tags)->put($cacheId, $res);
        }

        return $this->sendResponse(['albums' => $res]);
    }
}
