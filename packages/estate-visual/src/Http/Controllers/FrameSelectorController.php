<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Kelnik\EstateVisual\View\Components\SelectorFrame\SelectorFrame;
use Symfony\Component\HttpFoundation\Response;

final class FrameSelectorController extends Contracts\BaseSelectorController
{
    public function __invoke(Request $request): Response
    {
        try {
            $config = $this->initSearchConfig(Route::current()->parameter('cid'));
        } catch (InvalidArgumentException $e) {
            return $this->sendError(['Bad request'], [$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response((new SelectorFrame())->getIframe($request, $config));
    }
}
