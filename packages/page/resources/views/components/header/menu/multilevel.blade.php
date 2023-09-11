@if(!empty($menu) && $menu->exists && $menu->items->isNotEmpty())
    <div class="header__navigation j-header__nav">
        <nav class="navigation j-folding-navigation">
            <ul class="navigation__list">
                @foreach($menu->items as $item)
                    @include('kelnik-page::components.header.menu.partial.multilevel-el', ['item' => $item])
                @endforeach
            </ul>
        </nav>
    </div>
@endif
