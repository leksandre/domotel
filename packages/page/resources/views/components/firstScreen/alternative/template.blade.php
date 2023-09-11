@php
    $cssClasses = 'first-screen first-screen_theme_alternative';

    if (!empty($fullWidth)) {
        $cssClasses .= ' first-screen_theme_alternative-full';
    }

    $sliderCount = !empty($slider) ? $slider->count() : 0;

    if ($animated) {
        $cssClasses .= ' first-screen_with_animation';
    }

    $cssClasses .= ' j-main-screen';
@endphp
<section class="{{ $cssClasses }}" @if(!empty($alias))id="{{ $alias }}"@endif>
    @if(!empty($video))
        <div class="first-screen__background">
            <div class="first-screen__background-video j-first-screen__video" style="background-image: url('{!! $video['preview'] ?? '' !!}');">
                @if($video['name'] === 'youtube')
                    <div class="j-first-screen__youtube" data-id="{!! $video['id'] !!}"></div>
                @else
                    <iframe class="j-first-screen__iframe" src="{!! $video['loopPlayer'] !!}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                @endif
            </div>
        </div>
    @elseif($sliderCount)
        <div class="first-screen__background">
            @if($sliderCount > 1)
                <div class="first-screen__background-slider">
                    <div class="slider j-first-screen-slider">
                        <div class="slider__wrap j-slides">
                            @foreach($slider as $slide)
                                <div class="slider-first-screen">
                                    @if($slide instanceof \Orchid\Attachment\Models\Attachment)
                                        <img class="j-lazy" src="{{ $slide->url }}" alt="{{ $slide->alt }}">
                                    @else
                                        {!! $slide !!}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                @php
                    $slider = $slider->first();
                @endphp
                <div class="first-screen__background-picture">
                    @if($slider instanceof \Orchid\Attachment\Models\Attachment)
                        <img src="{{ $slider->url }}" alt="{{ $complexName }}">
                    @else
                        {!! $slider !!}
                    @endif
                </div>
            @endif
        </div>
    @endif
    <div class="first-screen__wrap">
        <div class="first-screen__top">
            <div class="first-screen__content">
                <div class="first-screen__content-container">
                    <div class="first-screen__header">
                        @if(!empty($complexName))
                            <div class="first-screen__slogan"><h1>{{ $complexName }}</h1></div>
                        @endif
                        @if(!empty($slogan))
                            <strong class="first-screen__title">{{ $slogan }}</strong>
                        @endif
                    </div>
                    @if($advantages)
                        <div class="first-screen__tags">
                            <ul class="tags tags_theme_alternative">
                                @foreach($advantages as $el)
                                    <li class="tag">{{ $el['title'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                @if(!empty($actionParams))
                    <x-kelnik-news-element :params="$actionParams" />
                @endif
            </div>
        </div>
        @if(!empty($estateParams))
            <x-kelnik-estate-stat-list :params="$estateParams" />
        @endif
    </div>
</section>
