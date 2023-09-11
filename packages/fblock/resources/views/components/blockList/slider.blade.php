<section class="section j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="flats-block">
        @include('kelnik-fblock::components.blockList.partials.content')
        @if(!empty($blocks) && $blocks->isNotEmpty())
            <div class="flats-block__gallery">
                <div class="grid">
                    <div class="grid__row">
                        <div class="grid__common">
                            <div class="grid__gallery j-animation__item">
                                <div class="slider slider_theme_flats-big-slider j-slider">
                                    <div class="slider__wrap j-slides">
                                        @foreach($blocks as $el)
                                            <div class="flat-block-card flat-block-card_theme_big-slider">
                                                <div class="flat-block-card__content">
                                                    <div class="flat-block-card__plan-wrap">
                                                        @if(!empty($el->planoplan_code))
                                                            @php
                                                                $popParams = [
                                                                    'uid' => $el->planoplan_code,
                                                                    'el' => $pageComponent . '-fblock_' . $el->id,
                                                                    'primaryColor' => $colors['primary'] ?? '#95d0a1',
                                                                    'secondaryColor' => '#F4F7F7',
                                                                    'textColor' => $colors['text'] ?? '#00000',
                                                                    'backgroundColor' => '#FFFFFF'
                                                                ];
                                                            @endphp
                                                            @pushonce('footer')
                                                                <script src="https://widget.planoplan.com/etc/multiwidget/release/static/js/main.js"></script>
                                                            @endpushonce
                                                            <div class="flat-block-card__widget j-slider__widget" id="{{ $popParams['el'] }}" data-planoplan="{!! base64_encode(json_encode($popParams)) !!}"></div>
                                                        @elseif($el->imageSlider)
                                                            @foreach($el->imageSlider as $slide)
                                                                {!! $slide['picture'] !!}
                                                            @endforeach
                                                        @elseif($el->relationLoaded('images') && $el->images->isNotEmpty())
                                                            @foreach($el->images as $slide)
                                                                <img loading="lazy" data-src="{!! $slide->url() !!}" alt="{{ $slide->alt ?? $el->title }}">
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    @include('kelnik-fblock::components.blockList.partials.detail', ['formClass' => 'j-popup-slider'])
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
