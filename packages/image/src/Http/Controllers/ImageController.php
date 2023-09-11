<?php

declare(strict_types=1);

namespace Kelnik\Image\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Kelnik\Core\Repositories\Contracts\AttachmentRepository;
use Kelnik\Image\Config;
use Kelnik\Image\Params;
use Kelnik\Image\Resizer;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;

final class ImageController extends Controller
{
    /**
     * @param Request $request
     * @return Application|RedirectResponse|\Illuminate\Http\Response|Redirector
     * @throws Exception
     */
    public function __invoke(Request $request): Application|RedirectResponse|\Illuminate\Http\Response|Redirector
    {
        $routeParams = Route::current();
        $imageConfig = new Config();
        $requestedFile = pathinfo($routeParams->parameter('filename'));
        $attachment = resolve(AttachmentRepository::class)->findByName($requestedFile['filename']);

        abort_if(!$attachment->exists, Response::HTTP_NOT_FOUND);

        $params = new Params();

        $params->width = self::getIntOrNullFromString($routeParams->parameter('width'));
        $params->height = self::getIntOrNullFromString($routeParams->parameter('height'));
        $params->crop = $routeParams->parameter('crop') === 'c/';
        $params->blur = $routeParams->parameter('blur') === 'b/';
        $params->filename = $requestedFile['basename'];

        $storageSrc = Storage::disk($attachment->disk);
        $storageDst = Storage::disk($imageConfig->storageDisk());
        $dstPath = $imageConfig->storagePath();
        $dstIsExternal = !($storageDst->getAdapter() instanceof LocalFilesystemAdapter);

        if ($params->width) {
            $dstPath .= DIRECTORY_SEPARATOR . 'w' . $params->width;
        }

        if ($params->height) {
            $dstPath .= DIRECTORY_SEPARATOR . 'h' . $params->height;
        }

        if ($params->crop) {
            $dstPath .= DIRECTORY_SEPARATOR . 'c';
        }

        if ($params->blur) {
            $dstPath .= DIRECTORY_SEPARATOR . 'b';
        }

        $dstPath .= DIRECTORY_SEPARATOR . $attachment->name . '.' . $requestedFile['extension'];

        abort_if(!$storageSrc->exists($attachment->physicalPath()), Response::HTTP_NOT_FOUND);

        if ($dstIsExternal && $storageDst->exists($dstPath)) {
            return redirect($storageDst->url($dstPath), Response::HTTP_MOVED_PERMANENTLY);
        }

        $imageBlob = $storageSrc->get($attachment->physicalPath());
        $imageBlob = (new Resizer($imageBlob, $imageConfig))->setParams($params)->getBlob();

        abort_if(!$imageBlob, Response::HTTP_NOT_FOUND);
        $storageDst->put($dstPath, $imageBlob);

        if ($dstIsExternal) {
            return redirect($storageDst->url($dstPath), Response::HTTP_MOVED_PERMANENTLY);
        }

        $image = Image::make($imageBlob);
        unset($imageBlob);

        return \Illuminate\Support\Facades\Response::make($image->encode())
            ->header('Content-type', $image->mime());
    }

    protected static function getIntOrNullFromString(?string $val = null): ?int
    {
        if ($val === null) {
            return $val;
        }

        $val = str_split($val);
        $val = array_filter($val, 'is_numeric');
        $val = implode('', $val);

        return strlen($val) ? (int)$val : null;
    }
}
