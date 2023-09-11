@if(!empty($menu) && $menu->exists && $menu->items->isNotEmpty())
    @php
        $itemsTheme = [];
        $items = [];
        foreach ($menu->items as $el) {
            if ($el->relationLoaded('icon') && $el->icon->exists) {
                $itemsTheme[] = $el;
                continue;
            }
            $items[] = $el;
        }
        unset($menu);
    @endphp
    <div class="header__mobile-menu">
        <nav class="navigation-mobile j-navigation-mobile">
            @if($itemsTheme)
            <ul class="navigation-mobile__list navigation-mobile__list_theme_search">
                @foreach($itemsTheme as $el)
                    <li class="navigation-mobile__list-item">
                        <a class="navigation-mobile__link" href="{!! $el->url !!}">
                            <img src="{!! $el->icon->url() !!}" alt="{{ $el->title }}" width="24" height="24">
                            {{ $el->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
            @endif
            @if($items)
            <ul class="navigation-mobile__list">
                @foreach($items as $item)
                    @php
                        $classNames = 'navigation-mobile__link';
                        $url = $item->url;
                        if ($item->selected) {
                            $url = parse_url($item->url, PHP_URL_FRAGMENT);
                            $url = $url ? '#' . $url : '';
                            $classNames .= ' j-anchor';
                        }
                    @endphp
                    <li class="navigation-mobile__list-item">
                        <a class="{{ $classNames }}" href="{!! $url !!}">{{ $item->title }}</a>
                    </li>
                @endforeach
            </ul>
            @endif
            @if(isset($phone) && isset($phoneLink))
                <div class="navigation-mobile__phone">
                    <a href="tel:{{ $phoneLink ?? '' }}" class="phone">
                        <span class="phone__number">{{ $phone ?? '' }}</span>
                    </a>
                </div>
            @endif
        </nav>
    </div>
@endif
