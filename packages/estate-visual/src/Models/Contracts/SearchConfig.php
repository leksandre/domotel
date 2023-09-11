<?php

declare(strict_types=1);

namespace Kelnik\EstateVisual\Models\Contracts;

abstract class SearchConfig
{
    public readonly array $types;
    public readonly array $statuses;
    public readonly array $filters;
    public readonly array $cacheTags;
    public readonly string $template;
    public readonly string $iframeType;
    public readonly string $plural;
    public readonly ?string $popup;
    public readonly array $form;

    public function __construct(array $settings)
    {
        $values = [
            'types' => [],
            'statuses' => [],
            'filters' => config('kelnik-estate-visual.filters'),
            'cacheTags' => [],
            'template' => '',
            'iframeType' => '',
            'plural' => '',
            'popup' => null,
            'form' => []
        ];

        foreach ($values as $k => $v) {
            $this->{$k} = $settings[$k] ?? $v;
        }
    }
}
