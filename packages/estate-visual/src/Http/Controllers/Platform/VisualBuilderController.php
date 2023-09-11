<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Controllers\Platform;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Kelnik\Core\Http\Controllers\BaseApiController;
use Kelnik\EstateVisual\Platform\Services\Contracts\SelectorPlatformService;

final class VisualBuilderController extends BaseApiController
{
    public function getData($selector, $element, Request $request): JsonResponse
    {
        try {
            return $this->sendResponse(
                resolve(SelectorPlatformService::class)->getBuilderData($selector, $element, $request)
            );
        } catch (Exception $e) {
            return $this->sendError([$e->getMessage()]);
        }
    }
}
