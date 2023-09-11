<?php

declare(strict_types=1);

namespace Kelnik\EstateSearch\Models\Contracts;

use Kelnik\Core\Models\Site;
use Kelnik\EstateSearch\Models\Enums\PaginationType;
use Kelnik\EstateSearch\Models\Enums\PaginationViewType;
use Kelnik\EstateSearch\View\Components\Search\Search;

abstract class SearchConfig
{
    public const PAGINATION_PER_PAGE_DEFAULT = 6;
    public const PAGINATION_PER_PAGE_MIN = 1;
    public const PAGINATION_PER_PAGE_MAX = 100;

    public readonly array $types;
    public readonly array $statuses;
    public readonly array $filters;
    public readonly array $orders;
    public readonly array $cacheTags;
    public readonly ?string $popup;
    public readonly int $view;
    public readonly Pagination $pagination;
    public readonly bool $switch;
    public readonly ?Site $site;

    public function __construct(array $settings)
    {
        $values = [
            'types' => [],
            'statuses' => [],
            'filters' => [],
            'orders' => [],
            'cacheTags' => [],
            'popup' => null,
            'view' => Search::VIEW_TYPE_CARD,
            'switch' => true,
            'pagination' => resolve(Pagination::class, [
                'type' => PaginationType::getDefault(),
                'viewType' => PaginationViewType::getDefault(),
                'perPage' => self::PAGINATION_PER_PAGE_DEFAULT,
            ]),
            'site' => null
        ];

        foreach ($values as $k => $v) {
            $this->{$k} = $settings[$k] ?? $v;
        }
    }
}
