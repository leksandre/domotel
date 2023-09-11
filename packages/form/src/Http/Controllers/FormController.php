<?php

declare(strict_types=1);

namespace Kelnik\Form\Http\Controllers;

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use InvalidArgumentException;
use Kelnik\Core\Http\Controllers\BaseApiController;
use Kelnik\Form\Services\Contracts\FormService;
use Symfony\Component\HttpFoundation\Response;

final class FormController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware([EncryptCookies::class, StartSession::class, VerifyCsrfToken::class]);
    }

    public function __invoke(int $id, Request $request): JsonResponse
    {
        try {
            /** @var FormService $formService */
            $formService = resolve(FormService::class, ['primary' => $id]);
        } catch (InvalidArgumentException $e) {
            return $this->sendError([], [$e->getMessage()]);
        }

        if (!$formService->submit($request)) {
            return $this->sendError([], $formService->getLastErrors(), Response::HTTP_PRECONDITION_FAILED);
        }

        return $this->sendResponse([]);
    }
}
