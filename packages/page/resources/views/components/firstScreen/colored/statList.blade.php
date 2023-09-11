<div class="first-screen__flats-compilation j-first-screen-offset">
    <div class="first-screen__content">
        <div class="first-screen__flats-compilation-blocks">
            @foreach($list as $el)
                @php
                    $hasPrice = !empty($el['priceMin']);
                    $hasArea = !empty($el['areaMin']);
                @endphp
                <a class="flats-compilation" href="{!! $el['url'] ?? '' !!}">
                    <div class="flats-compilation__heading">{{ $el['title'] }}</div>
                    <ul class="flats-compilation__list">
                        @if($hasPrice)
                            <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.priceMin', ['val' => number_format($el['priceMin'] / 1_000_000, 1, '.')]) }}</li>
                        @endif
                        @if($hasArea)
                            <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.areaMin', ['val' => $el['areaMin']]) }}</li>
                        @endif
                    </ul>
                    <div class="flats-compilation__list-plus">
                        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 3.16666C11.0523 3.16666 11.5 3.61437 11.5 4.16666V8.99999H16.3333C16.8856 8.99999 17.3333 9.4477 17.3333 9.99999C17.3333 10.5523 16.8856 11 16.3333 11H11.5V15.8333C11.5 16.3856 11.0523 16.8333 10.5 16.8333C9.94772 16.8333 9.5 16.3856 9.5 15.8333V11H4.66667C4.11439 11 3.66667 10.5523 3.66667 9.99999C3.66667 9.4477 4.11439 8.99999 4.66667 8.99999H9.5V4.16666C9.5 3.61437 9.94772 3.16666 10.5 3.16666Z" />
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="first-screen__flats-compilation-slider">
            <div class="slider slider_theme_flats-compilation j-flats-compilation-slider">
                <div class="slider__wrap j-slides">
                    @foreach($list as $el)
                        @php
                            $hasPrice = !empty($el['priceMin']);
                            $hasArea = !empty($el['areaMin']);
                        @endphp
                        <a class="flats-compilation" href="{!! $el['url'] ?? '' !!}">
                            <div class="flats-compilation__heading">{{ $el['title'] }}</div>
                            <ul class="flats-compilation__list">
                                @if($hasPrice)
                                    <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.priceMin', ['val' => number_format($el['priceMin'] / 1_000_000, 1, '.')]) }}</li>
                                @endif
                                @if($hasArea)
                                    <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.areaMin', ['val' => $el['areaMin']]) }}</li>
                                @endif
                            </ul>
                            <div class="flats-compilation__list-plus">
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 3.16666C11.0523 3.16666 11.5 3.61437 11.5 4.16666V8.99999H16.3333C16.8856 8.99999 17.3333 9.4477 17.3333 9.99999C17.3333 10.5523 16.8856 11 16.3333 11H11.5V15.8333C11.5 16.3856 11.0523 16.8333 10.5 16.8333C9.94772 16.8333 9.5 16.3856 9.5 15.8333V11H4.66667C4.11439 11 3.66667 10.5523 3.66667 9.99999C3.66667 9.4477 4.11439 8.99999 4.66667 8.99999H9.5V4.16666C9.5 3.61437 9.94772 3.16666 10.5 3.16666Z" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
