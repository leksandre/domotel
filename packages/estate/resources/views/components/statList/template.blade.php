<div class="first-screen__flats-compilation">
    <div class="first-screen__content">
        <div class="first-screen__flats-compilation-blocks">
            @foreach($list as $el)
                @php
                    $hasPrice = !empty($el['priceMin']);
                    $hasArea = !empty($el['areaMin']);
                @endphp
                <a class="flats-compilation" href="{!! $el['url'] ?? '' !!}">
                    <div class="flats-compilation__heading">{{ $el['title'] }}</div>
                    @if($hasPrice || $hasArea)
                        <ul class="flats-compilation__list">
                            @if($hasPrice)
                                <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.priceMin', ['val' => number_format($el['priceMin'] / 1_000_000, 1, '.')]) }}</li>
                            @endif
                            @if($hasArea)
                                <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.areaMin', ['val' => $el['areaMin']]) }}</li>
                            @endif
                        </ul>
                    @endif
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
                            @if($hasPrice || $hasArea)
                                <ul class="flats-compilation__list">
                                    @if($hasPrice)
                                        <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.priceMin', ['val' => number_format($el['priceMin'] / 1_000_000, 1, '.')]) }}</li>
                                    @endif
                                    @if($hasArea)
                                        <li class="flats-compilation__list-item">{{ trans('kelnik-estate::front.components.statList.areaMin', ['val' => $el['areaMin']]) }}</li>
                                    @endif
                                </ul>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
