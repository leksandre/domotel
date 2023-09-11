<?php

declare(strict_types=1);

namespace Kelnik\Estate\Models\Planoplan\Contracts;

use Illuminate\Support\Arr;

abstract class Widget
{
    public const PLAN_TOP = 'planTop';
    public const PLAN_3D = 'plan3d';
    public const PLAN_3D_ANGLE = 'anglePlan3d';

    public function __construct(protected readonly array $data)
    {
    }

    public function plan(): ?string
    {
        $method = config('kelnik-estate.planoplan.widget.plan');

        return method_exists($this, $method) ? $this->{$method}() : null;
    }

    abstract public static function dataUrl(string $uid): string;

    abstract public function planTop(): ?string;

    abstract public function plan3d(): ?string;

    abstract public function anglePlan3d(): ?string;

    /** @return string[] */
    abstract public function plan360(): array;

    abstract public function tourLink(): ?string;

    abstract public function qrCodeLink(): ?string;

    protected function id(): int
    {
        return (int)Arr::get($this->data, 'designs.0.id', 0);
    }

    protected function project(): array
    {
        return Arr::get($this->data, 'designs.0.project', []);
    }

    protected function floorId(): int
    {
        return (int)current(Arr::wrap(Arr::get($this->project(), 'floors_order')));
    }
}
