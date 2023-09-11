<?php

declare(strict_types=1);

namespace Kelnik\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Kelnik\Core\Services\Contracts\UploadService;
use Symfony\Component\HttpFoundation\Response;

final class FileController extends Controller
{
    public function __construct(private readonly UploadService $uploadService)
    {
    }

    public function uploadChunk(Request $request): JsonResponse
    {
        $fileId = $request->post('dzuuid');
        $currentChunk = (int)$request->post('dzchunkindex');
        $totalChunks = (int)$request->post('dztotalchunkcount');

        $isComplete = ($currentChunk + 1) === $totalChunks;
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('files');

        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return $this->response([
                'status' => 'error',
                'error' => $uploadedFile->getErrorMessage()
            ]);
        }

        $this->uploadService->saveChunk($fileId, $currentChunk, UploadedFile::createFromBase($uploadedFile));

        if (!$isComplete) {
            return $this->response(['status' => 'uploading']);
        }

        $uploadedFile = $this->uploadService->compileFileFromChunks(
            $fileId,
            $totalChunks,
            (int)$request->post('dztotalfilesize')
        );

        if (!$uploadedFile) {
            $this->response([
                'status' => 'error',
                'error' => 'Internal error, upload failed'
            ]);
        }

        $attachment = $this->uploadService->createAttachment(
            $uploadedFile,
            $request->post('storage', 'public'),
            $request->post('group')
        );
        unlink($uploadedFile->getRealPath());
        $res = $attachment->toArray();
        $res['status'] = 'success';

        return $this->response($res);
    }

    private function response(array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $status);
    }
}
