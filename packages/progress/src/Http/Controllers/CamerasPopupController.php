<?php

declare(strict_types=1);

namespace Kelnik\Progress\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Kelnik\Progress\Http\Resources\CameraPopupResource;
use Kelnik\Progress\Repositories\Contracts\CameraRepository;
use Kelnik\Progress\Services\Contracts\ProgressService;

final class CamerasPopupController extends ProgressController
{
    public function __construct(
        private ProgressService $progressService,
        private CameraRepository $cameraRepository
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $group = $this->getGroupId();

        $cacheId = $this->progressService->getCameraListCacheTag() . '_popup' . ($group ? '_' . $group : '');
        $res = Cache::get($cacheId);

        if ($res === null) {
            $res = CameraPopupResource::collection($this->cameraRepository->getActive($group));
            $tags = [$this->progressService->getCameraListCacheTag()];

            if ($group) {
                $tags[] = $this->progressService->getGroupCacheTag($group);
            }

            Cache::tags($tags)->put($cacheId, $res);
        }

        return $this->sendResponse(['cameras' => $res]);
    }
}
