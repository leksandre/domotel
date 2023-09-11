<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Kelnik\EstateVisual\Http\Handlers\Contracts\StepHandler;
use Kelnik\EstateVisual\Http\Resources\StepResource;
use Symfony\Component\HttpFoundation\Response;

final class SelectorController extends Contracts\BaseSelectorController
{
    public function __invoke(Request $request): JsonResponse
    {
        $errors = [];

        try {
            $requestType = $request->get(self::PARAM_REQUEST_STEP);
            $handlerClass = 'Kelnik\\EstateVisual\\Http\\Handlers\\' . ucfirst($requestType);
            $config = $this->initSearchConfig(Route::current()->parameter('cid'));

            if ($requestType && class_exists($handlerClass)) {
                /** @var StepHandler $handler */
                $handler = new $handlerClass((int)Route::current()->parameter('id'), $config);
                $res = $handler->handle($request->post());
                $res->put('config', $config);

                return $this->sendResponse(
                    StepResource::make($res)->resolve($request)
                );
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }

        return $this->sendError(['Bad request'], $errors, Response::HTTP_BAD_REQUEST);
    }
}
