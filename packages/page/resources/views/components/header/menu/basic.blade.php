@if(!empty($menu) && $menu->exists && $menu->items->isNotEmpty())
    <div class="header__navigation j-header__nav">
        <nav class="navigation j-folding-navigation">
            <ul class="navigation__list">
                @foreach($menu->items as $item)
                    @php
                        $classNames = 'navigation__link';
                        $url = $item->url;
                        if ($item->selected) {
                            $url = parse_url($item->url, PHP_URL_FRAGMENT);
                            $url = $url ? '#' . $url : '';
                            $classNames .= ' j-anchor';
                        }
                    @endphp
                    <li class="navigation__item">
                        <a class="{{ $classNames }}" href="{!! $url !!}">{{ $item->title }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
@endif
