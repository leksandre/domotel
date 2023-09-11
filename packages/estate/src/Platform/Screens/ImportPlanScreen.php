<?php

declare(strict_types=1);

namespace Kelnik\Estate\Platform\Screens;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Http\Controllers\Traits\ApiResponse;
use Kelnik\Estate\Platform\Layouts\ImportPlan\Form;
use Kelnik\Estate\Platform\Services\Contracts\ImportPlanPlatformService;
use Kelnik\Estate\Repositories\Contracts\ComplexRepository;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Symfony\Component\HttpFoundation\Response;

final class ImportPlanScreen extends Screen
{
    use ApiResponse;

    private readonly ImportPlanPlatformService $importPlanPlatformService;
    protected string $name;

    public function __construct()
    {
        $this->name = trans('kelnik-estate::admin.menu.importPlan');
        $this->importPlanPlatformService = resolve(ImportPlanPlatformService::class);
    }

    public function query(): array
    {
        return [
            'complexes' => resolve(ComplexRepository::class)->getAllForAdmin()
        ];
    }

    /** @return Layout[]|string[] */
    public function layout(): array
    {
        return [
            Form::class
        ];
    }

    public function getComplexChilds(Request $request): JsonResponse
    {
        $cid = (int)$request->post('cid');

        abort_if(!$cid, Response::HTTP_BAD_REQUEST);

        return $this->sendResponse([
            'buildings' => $this->importPlanPlatformService->getBuildingsByComplex($cid)
        ]);
    }

    public function getBuildingChilds(Request $request): JsonResponse
    {
        $bid = (int)$request->post('bid');

        abort_if(!$bid, Response::HTTP_BAD_REQUEST);

        return $this->sendResponse([
            'sections' => $this->importPlanPlatformService->getSectionsByBuilding($bid),
            'floors' => $this->importPlanPlatformService->getFloorsByBuilding($bid)
        ]);
    }

    public function getSectionChilds(Request $request): JsonResponse
    {
        $sid = (int)$request->post('sid');

        abort_if(!$sid, Response::HTTP_BAD_REQUEST);

        return $this->sendResponse([
            'floors' => $this->importPlanPlatformService->getFloorsBySection($sid)
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $type = $request->post('type');
        $field = Arr::get(
            [
                'plan' => 'image_plan_id',
                'searchPlan' => 'image_list_id',
                'floorPlan' => 'image_on_floor_id'
            ],
            $request->post('field')
        );
        $section = $request->integer('section');
        $floors = (array)$request->post('floors');
        $file = $request->file('file');

        if (!$file) {
            return $this->sendError(
                message: trans('kelnik-estate::admin.import.errors.fileRequired'),
                code: Response::HTTP_BAD_REQUEST
            );
        }

        if (!$file->isValid()) {
            return $this->sendError(
                message: trans('kelnik-estate::admin.import.errors.fileUploadError'),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if (!$floors) {
            return $this->sendError(
                message: trans('kelnik-estate::admin.import.errors.floorListRequired'),
                code: Response::HTTP_BAD_REQUEST
            );
        }

        if (!$field) {
            return $this->sendError(
                message: trans('kelnik-estate::admin.import.errors.invalidImageField'),
                code: Response::HTTP_BAD_REQUEST
            );
        }

        $updatedCnt = $this->importPlanPlatformService->import($type, $field, $section, $floors, $file);

        return $this->sendResponse([
            'updated' => trans('kelnik-estate::admin.import.result', ['cnt' => $updatedCnt])
        ]);
    }
}
