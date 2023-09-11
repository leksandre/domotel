@php
    $classNames = 'navigation-mobile__link';
    $url = $item->url;
    if ($item->selected) {
        $url = parse_url($item->url, PHP_URL_FRAGMENT);
        $url = $url ? '#' . $url : '';
        $classNames .= ' j-anchor';
    }
@endphp

<li class="navigation-mobile__list-item @if($item->marked)navigation-mobile__list-item_theme_search @endif">
    <a class="{{ $classNames }}" href="{!! $url !!}">
        @if($item->iconBody)
            {!! $item->iconBody !!}
        @elseif($item->icon->exists)
            <img src="{!! $item->icon->url !!}" alt="{{ $item->title }}" width="24" height="24" />
        @endif
        {{ $item->title }}
    </a>
</li>
