<?php

declare(strict_types=1);

namespace Kelnik\Page\Services\Contracts;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

interface HttpErrorService
{
    public const EXCEPTIONS = [
        Response::HTTP_UNAUTHORIZED => UnauthorizedHttpException::class,
        Response::HTTP_FORBIDDEN => AccessDeniedHttpException::class,
        Response::HTTP_NOT_FOUND => NotFoundHttpException::class,

        Response::HTTP_INTERNAL_SERVER_ERROR => HttpException::class,
        Response::HTTP_SERVICE_UNAVAILABLE => ServiceUnavailableHttpException::class,
    ];

    public const DEFAULT_ERROR_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;
    public const PAGE_PATH_SLUG = 'error-handler';

    public function executable(HttpExceptionInterface $e, Request $request): bool;

    public function handle(HttpExceptionInterface $e, Request $request): ?\Illuminate\Http\Response;
}
