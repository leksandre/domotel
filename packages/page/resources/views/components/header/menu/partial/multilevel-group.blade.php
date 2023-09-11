<li class="navigation__item">
    <ul class="navigation-group j-group-navigation">
        <li class="navigation-group__item j-group-navigation__item j-group-navigation__navigation" data-group="true" data-show="960">
            <div class="navigation-group__grouper j-group-navigation__grouper">
                <div class="navigation-group__grouper-item">
                    {{ $item->title }}
                    <span class="navigation-group__grouper-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 10L12 14L8 10" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                </div>
                <nav class="navigation j-group-navigation__navigation">
                    <ul class="navigation__list">
                        @foreach($item->children as $subItem)
                            @include('kelnik-page::components.header.menu.partial.multilevel-el', ['item' => $subItem])
                        @endforeach
                    </ul>
                </nav>
            </div>
        </li>
    </ul>
</li>
