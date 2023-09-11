@if(!empty($menu) && $menu->exists && $menu->items->isNotEmpty())
    <div class="header__search-navigation">
        <ul class="navigation-group j-group-navigation">
            @foreach($menu->items as $el)
                @if($el->url === '#sub1')
                    <li class="navigation-group__item j-group-navigation__item" data-group="true" data-show="960">
                        <div class="navigation-group__grouper j-group-navigation__grouper">
                            {{ $el->title }}
                            <span class="navigation-group__grouper-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 10L12 14L8 10" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </span>
                            @if($el->relationLoaded('children'))
                                <nav class="navigation j-group-navigation__navigation">
                                    <ul class="navigation__list">
                                        @foreach($el->children as $subEl)
                                            @php
                                                $classNames = 'navigation__link';
                                                $url = $subEl->url;
                                                if ($subEl->selected) {
                                                    $url = parse_url($subEl->url, PHP_URL_FRAGMENT);
                                                    $url = $url ? '#' . $url : '';
                                                    $classNames .= ' j-anchor';
                                                }
                                            @endphp
                                            <li class="navigation__item">
                                                <a class="{{ $classNames }}" href="{!! $url !!}">{{ $subEl->title }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </li>
                @elseif($el->url === '#sub2' && $el->relationLoaded('children'))
                    <li class="navigation-group__item j-group-navigation__item" data-group="true" data-show="960" data-group-start="960" data-group-end="1280">
                        <div class="navigation-group__grouper j-group-navigation__grouper">
                            {{ $el->title }}
                            <span class="navigation-group__grouper-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 10L12 14L8 10" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </span>
                            <nav class="navigation j-group-navigation__navigation">
                                <ul class="navigation__list">
                                    @foreach($el->children as $subEl)
                                        <li class="navigation__item">
                                            <a class="navigation__link" href="{!! $subEl->url !!}">{{ $subEl->title }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        </div>
                    </li>
                @elseif($el->url === '#sub3' && $el->relationLoaded('children'))
                    <li class="navigation-group__item j-group-navigation__item" data-show="{{ 960 + ($el->children->count() * 50) }}">
                        <nav class="navigation">
                            <ul class="navigation__list">
                                @foreach($el->children as $subEl)
                                    <li class="navigation__item">
                                        <a class="navigation__link" href="{!! $subEl->url !!}">{{ $subEl->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif
