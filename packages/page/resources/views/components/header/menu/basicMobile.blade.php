<div class="header__mobile-menu">
    <nav class="navigation-mobile j-navigation-mobile">
        @if(!empty($menu) && $menu->exists && $menu->items->isNotEmpty())
            <ul class="navigation-mobile__list">
                @foreach($menu->items as $item)
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
                <a href="tel:{{ $phoneLink ?? '' }}" class="phone"><span class="phone__number">{{ $phone ?? '' }}</span></a>
            </div>
        @endif
    </nav>
</div>
