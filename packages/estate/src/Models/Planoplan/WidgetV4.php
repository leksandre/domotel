<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Planoplan;

use Illuminate\Support\Arr;

final class WidgetV4 extends Contracts\Widget
{
    public static function dataUrl(string $uid): string
    {
        return 'https://widget.planoplan.com/data/v4/?hash=' . $uid;
    }

    public function planTop(): ?string
    {
        return Arr::get($this->project(), 'tree.plan_' . $this->floorId() . '_' . $this->id() . '.image_1x');
    }

    public function plan3d(): ?string
    {
        return Arr::get($this->project(), 'tree.top_' . $this->floorId() . '_' . $this->id() . '.image_1x');
    }

    public function anglePlan3d(): ?string
    {
        return Arr::get($this->project(), 'tree.three_quarter_' . $this->floorId() . '_' . $this->id() . '.image_1x');
    }

    public function plan360(): array
    {
        return Arr::get(
            $this->project(),
            'tree.rotate_360_' . $this->floorId() . '_' . $this->id() . '.image',
            []
        );
    }

    public function tourLink(): ?string
    {
        return Arr::get($this->project(), 'tree.virtual_tour.src');
    }

    public function qrCodeLink(): ?string
    {
        return Arr::get($this->data, 'designs.0.qr_code');
    }
}
