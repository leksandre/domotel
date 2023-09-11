<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Platform\Services;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kelnik\Core\Services\Contracts\CoreService;
use Kelnik\FBlock\Models\Button;
use Kelnik\FBlock\Models\FlatBlock;
use Kelnik\FBlock\Repositories\Contracts\BlockRepository;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Field;
use Orchid\Support\Facades\Toast;

final class BlockPlatformService implements Contracts\BlockPlatformService
{
    public const FEATURE_MAX_COUNT = 20;
    public const BUTTON_TEXT_LIMIT = 100;
    public const NO_VALUE = '0';

    public function __construct(private readonly BlockRepository $repository, private readonly CoreService $coreService)
    {
    }

    public function saveElementFromPlatform(FlatBlock $block, Request $request): RedirectResponse
    {
        $elementData = $request->only('element');
        $rules = [
            'element.title' => 'required|max:255',
            'element.area' => 'nullable|max:255',
            'element.floor' => 'nullable|max:255',
            'element.price' => 'nullable|max:255',
            'element.button.text' => 'nullable|max:' . self::BUTTON_TEXT_LIMIT,
            'element.features.*.title' => 'nullable|max:255'
        ];

        $request->validate($rules);

        $elementData = Arr::get($elementData, 'element');
        $elementData['features'] = array_slice($elementData['features'] ?? [], 0, self::FEATURE_MAX_COUNT);
        $elementData['features'] = Arr::pluck($elementData['features'] ?? [], 'title');
        unset($elementData['images'], $elementData['button']);
        $block->fill($elementData);

        $block->button = null;
        $button = $request->input('element.button', []);
        if (!empty($button['text']) || !empty($button['formKey'])) {
            $block->button = new Button((int)($button['formKey'] ?? self::NO_VALUE), $button['text'] ?? null);
        }

        $this->repository->save($block, $request->input('element.images') ?? []);

        Toast::info(trans('kelnik-fblock::admin.saved'));

        return redirect()->route($this->coreService->getFullRouteName('fblock.elements'));
    }

    public function sortElements(array $elementPriority): bool
    {
        $repository = $this->repository;
        $elements = $repository->getAll();

        if ($elements->isEmpty()) {
            return false;
        }

        $elements->each(static function (FlatBlock $el) use ($elementPriority, $repository) {
            $el->priority = (int)array_search($el->getKey(), $elementPriority) + FlatBlock::PRIORITY_DEFAULT;
            $repository->save($el);
        });

        return true;
    }

    public function getContentLink(): Field
    {
        return Link::make(trans('kelnik-fblock::admin.blocks'))
            ->route($this->coreService->getFullRouteName('fblock.elements'))
            ->icon('info')
            ->class('btn btn-info')
            ->target('_blank')
            ->style('display: inline-block; margin-bottom: 20px');
    }
}
