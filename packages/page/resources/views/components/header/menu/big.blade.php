@if(!empty($menu) && $menu->exists && $menu->items->isNotEmpty())
    @php
        $rows = $blocks = [];
        foreach ($menu->items as $item) {
            if (!empty($item['params']['isRow'])) {
                $rows[] = $item;
                continue;
            }

            $blocks[] = $item;
        }
        unset($menu);
    @endphp
    <div class="header__mobile-menu">
        <div class="navigation-fullscreen__menu j-navigation-fullscreen__menu">
            <div class="navigation-fullscreen__menu-wrapper">
                @if($rows)
                    <div class="navigation-fullscreen__main navigation-fullscreen__main_theme_odd navigation-fullscreen__main_theme_eight">
                        @foreach($rows as $row)
                            @if($row->relationLoaded('children') && $row->children->isNotEmpty())
                                <div @class([
                                        'navigation-fullscreen__main-row',
                                        'navigation-fullscreen__main-row_theme_odd' => ($row->children->count() % 2) > 0])>
                                    @foreach($row->children as $el)
                                        <a href="{!! $el->url !!}" class="navigation-fullscreen__main-item">
                                            <div class="navigation-fullscreen__main-ico">
                                                @if($el->iconBody)
                                                    {!! $el->iconBody !!}
                                                @elseif($el->icon->exists)
                                                    <img src="{!! $el->icon->url !!}" alt="{{ $el->title }}" width="24" height="24" />
                                                @else
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11 7C11 6.44772 11.4477 6 12 6H19C19.5523 6 20 6.44772 20 7C20 7.55228 19.5523 8 19 8H12C11.4477 8 11 7.55228 11 7Z" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 12C4 11.4477 4.44772 11 5 11H19C19.5523 11 20 11.4477 20 12C20 12.5523 19.5523 13 19 13H5C4.44772 13 4 12.5523 4 12Z" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 17C4 16.4477 4.44772 16 5 16H11C11.5523 16 12 16.4477 12 17C12 17.5523 11.5523 18 11 18H5C4.44772 18 4 17.5523 4 17Z" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="navigation-fullscreen__main-text">{{ $el->title }}</div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
                @if($blocks)
                    <div class="navigation-fullscreen__menu-sub details">
                        @foreach($blocks as $block)
                            @if($block->relationLoaded('children') && $block->children->isNotEmpty())
                                <details class="navigation-fullscreen__sub-group j-details" data-desktop-open="true">
                                    <summary class="navigation-fullscreen__sub-title">
                                        <a href="{!! $block->url !!}">{{ $block->title }}</a>
                                        <span class="navigation-fullscreen__sub-ico">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.29289 9.29289C7.68342 8.90237 8.31658 8.90237 8.70711 9.29289L12 12.5858L15.2929 9.29289C15.6834 8.90237 16.3166 8.90237 16.7071 9.29289C17.0976 9.68342 17.0976 10.3166 16.7071 10.7071L12 15.4142L7.29289 10.7071C6.90237 10.3166 6.90237 9.68342 7.29289 9.29289Z" />
                                            </svg>
                                        </span>
                                    </summary>
                                    <div class="navigation-fullscreen__sub-list j-details__content">
                                        @foreach($block->children as $el)
                                            <a href="{!! $el->url !!}" class="navigation-fullscreen__sub-item">{!! $el->title !!}</a>
                                        @endforeach
                                    </div>
                                </details>
                            @else
                                <div class="navigation-fullscreen__sub-group">
                                    <a href="{!! $block->url !!}" class="navigation-fullscreen__sub-title">{{ $block->title }}</a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
