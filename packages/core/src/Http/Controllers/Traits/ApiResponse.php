<?php

declare(strict_types=1);

namespace Kelnik\Core\Http\Controllers\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

trait ApiResponse
{
    /**
     * @param array $data
     * @param string[] $messages
     * @param int $code
     *
     * @return JsonResponse
     */
    public function sendResponse(
        array $data,
        array $messages = [],
        int $code = \Symfony\Component\HttpFoundation\Response::HTTP_OK
    ): JsonResponse {
        return Response::json(
            [
                'success' => true,
                'data' => $data,
                'messages' => $messages
            ],
            $code
        );
    }

    /**
     * @param string[] $message
     * @param string[] $errors
     * @param int $code
     *
     * @return JsonResponse
     */
    public function sendError(
        array $message,
        array $errors = [],
        int $code = \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
    ): JsonResponse {
        return Response::json(
            [
                'success' => false,
                'messages' => $message,
                'errors' => $errors
            ],
            $code
        );
    }
}
