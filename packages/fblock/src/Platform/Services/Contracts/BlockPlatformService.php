<?php

declare(strict_types=1);

namespace Kelnik\FBlock\Platform\Services\Contracts;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Kelnik\FBlock\Models\FlatBlock;
use Orchid\Screen\Field;

interface BlockPlatformService
{
    public function saveElementFromPlatform(FlatBlock $block, Request $request): RedirectResponse;

    public function sortElements(array $elementPriority): bool;

    public function getContentLink(): Field;
}
