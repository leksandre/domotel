@if(!empty($menu) && $menu->exists && $menu->items->isNotEmpty())
    <div class="header__mobile-menu">
        <nav class="navigation-mobile j-navigation-mobile">
            <ul class="navigation-mobile__list">
                @foreach($menu->items as $item)
                    @include('kelnik-page::components.header.menu.partial.multilevelMobile-el', ['item' => $item])
                @endforeach
            </ul>
            @if(isset($phone) && isset($phoneLink))
                <div class="navigation-mobile__phone">
                    <a href="tel:{{ $phoneLink ?? '' }}" class="phone"><span class="phone__number">{{ $phone ?? '' }}</span></a>
                </div>
            @endif
        </nav>
    </div>
@endif
