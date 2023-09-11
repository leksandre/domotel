<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Platform\Layouts\StepElement;

use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\EstateVisual\Providers\EstateVisualServiceProvider;
use Orchid\Screen\Layout;
use Orchid\Screen\Repository;

final class VisualLayout extends Layout
{
    public function build(Repository $repository)
    {
        /** @var CoreService $coreService */
        $coreService = $repository->get('coreService');

        $data = [
            'translates' => base64_encode(json_encode([
                'title' => trans('kelnik-estate-visual::admin.visual.title'),
                'angle' => trans('kelnik-estate-visual::admin.visual.angle'),
                'addAngle' => trans('kelnik-estate-visual::admin.visual.addAngle'),
                'uploadImage' => trans('kelnik-estate-visual::admin.visual.uploadImage'),
                'uploadMasks' => trans('kelnik-estate-visual::admin.visual.uploadMasks'),
                'confirmDeleteAngle' => trans('kelnik-estate-visual::admin.visual.confirmDeleteAngle'),
                'masks' => trans('kelnik-estate-visual::admin.visual.masks'),
                'data' => trans('kelnik-estate-visual::admin.visual.data'),
                'emptyList' => trans('kelnik-estate-visual::admin.visual.emptyList'),
                'error' => trans('kelnik-estate-visual::admin.visual.error'),
                'modified' => trans('kelnik-estate-visual::admin.visual.modified'),
                'fileNotLoaded' => trans('kelnik-estate-visual::admin.visual.fileNotLoaded'),
                'mask' => trans('kelnik-estate-visual::admin.visual.mask'),
                'maskNotFound' => trans('kelnik-estate-visual::admin.visual.maskNotFound'),
                'maskAdded' => trans('kelnik-estate-visual::admin.visual.maskAdded'),
                'maskNotAdded' => trans('kelnik-estate-visual::admin.visual.maskNotAdded'),
                'maskDeleted' => trans('kelnik-estate-visual::admin.visual.maskDeleted'),
                'maskLinkRemoved' => trans('kelnik-estate-visual::admin.visual.maskLinkRemoved'),
                'maskLinked' => trans('kelnik-estate-visual::admin.visual.maskLinked'),
                'maskUpdated' => trans('kelnik-estate-visual::admin.visual.maskUpdated'),
                'confirmDeleteMask' => trans('eklnik-estate-visual::admin.visual.confirmDeleteMask'),
                'invalidFileFormat' => trans('kelnik-estate-visual::admin.visual.invalidFileFormat'),
                'getImageSizesError' => trans('kelnik-estate-visual::admin.visual.getImageSizesError'),
                'uploadError' => trans('kelnik-estate-visual::admin.visual.uploadError'),
                'imageUploaded' => trans('kelnik-estate-visual::admin.visual.imageUploaded'),
                'errorLoadingData' => trans('kelnik-estate-visual::admin.visual.errorLoadingData'),
                'setLink' => trans('kelnik-estate-visual::admin.visual.setLink'),
                'nextStepHeader' => trans('kelnik-estate-visual::admin.visual.nextStepHeader'),
                'nextStep' => trans('kelnik-estate-visual::admin.visual.nextStep'),
                'coords' => trans('kelnik-estate-visual::admin.visual.coords'),
                'binding' => trans('kelnik-estate-visual::admin.visual.binding'),
                'noBinding' => trans('kelnik-estate-visual::admin.visual.noBinding'),
                'close' => trans('kelnik-estate-visual::admin.visual.close'),
                'link' => trans('kelnik-estate-visual::admin.visual.link'),
                'apply' => trans('kelnik-estate-visual::admin.visual.apply'),
                'delete' => trans('kelnik-estate-visual::admin.visual.delete'),
                'format' => trans('kelnik-estate-visual::admin.visual.format'),
                'uploadSvg' => trans('kelnik-estate-visual::admin.visual.uploadSvg'),
                'addingMasks' => trans('kelnik-estate-visual::admin.visual.addingMasks'),
                'degree' => trans('kelnik-estate-visual::admin.visual.degree'),
                'pointer' => trans('kelnik-estate-visual::admin.visual.pointer'),
                'shiftHorizontal' => trans('kelnik-estate-visual::admin.visual.shift.horizontal'),
                'shiftVertical' => trans('kelnik-estate-visual::admin.visual.shift.vertical'),
                'pointers' => trans('kelnik-estate-visual::admin.visual.pointers'),
                'addPointer' => trans('kelnik-estate-visual::admin.visual.addPointer'),
                'pointerTitle' => trans('kelnik-estate-visual::admin.visual.pointerTitle'),
                'pointerPopWidgetCode' => trans('kelnik-estate-visual::admin.visual.pointerPopWidgetCode'),
                'pointerCoords' => trans('kelnik-estate-visual::admin.visual.pointerCoords'),
                'autoLink' => trans('kelnik-estate-visual::admin.visual.autoLink')
            ])),
            'route' => route('platform.systems.files.upload'),
            'storageDisk' => config('platform.attachment.disk'),
            'groups' => EstateVisualServiceProvider::MODULE_NAME,
            'url' => route(
                $coreService->getFullRouteName('estateVisual.selector.step.data'),
                [
                    'selector' => $repository->get('step')?->getSelector(),
                    'element' => $repository->get('element')
                ]
            )
        ];

        return view('kelnik-estate-visual::platform.layouts.visual', $data);
    }
}
