@php
    $cssClasses = 'first-screen';

    $heightClasses = [
        'disabled' => null,
        'all' => ' first-screen_full_height',
        'desktop' => ' first-screen_full_height-laptop'
    ];
    if (!empty($fullHeight)) {
        $cssClasses .= $heightClasses[$fullHeight] ?? null;
    }

    $sliderCount = !empty($slider) ? $slider->count() : 0;

    if ($animated) {
        $cssClasses .= ' first-screen_with_animation';
    }

    if (isset($estateParams)) {
        $cssClasses .= ' first-screen_with_search';
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
                            <ul class="tags tags_theme_colorful">
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
        <button class="first-screen__scroll j-first-screen-scroll" aria-label="{{ trans('kelnik-page::front.components.firstScreen.scrollDown') }}">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <title></title>
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M16 1.49902C12.4101 1.49902 9.5 4.40917 9.5 7.99902V16.999C9.5 20.5889 12.4101 23.499 16 23.499C19.5899 23.499 22.5 20.5889 22.5 16.999V7.99902C22.5 4.40917 19.5899 1.49902 16 1.49902ZM8.5 7.99902C8.5 3.85689 11.8579 0.499023 16 0.499023C20.1421 0.499023 23.5 3.85689 23.5 7.99902V16.999C23.5 21.1412 20.1421 24.499 16 24.499C11.8579 24.499 8.5 21.1412 8.5 16.999V7.99902Z"/>
                <path
                    d="M17.3996 6.59922C17.3996 5.82602 16.7728 5.19922 15.9996 5.19922C15.2264 5.19922 14.5996 5.82602 14.5996 6.59922V9.39922C14.5996 10.1724 15.2264 10.7992 15.9996 10.7992C16.7728 10.7992 17.3996 10.1724 17.3996 9.39922V6.59922Z"/>
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M15.9996 5.69922C15.5026 5.69922 15.0996 6.10216 15.0996 6.59922V9.39922C15.0996 9.89628 15.5026 10.2992 15.9996 10.2992C16.4967 10.2992 16.8996 9.89628 16.8996 9.39922V6.59922C16.8996 6.10216 16.4967 5.69922 15.9996 5.69922ZM14.0996 6.59922C14.0996 5.54988 14.9503 4.69922 15.9996 4.69922C17.049 4.69922 17.8996 5.54988 17.8996 6.59922V9.39922C17.8996 10.4486 17.049 11.2992 15.9996 11.2992C14.9503 11.2992 14.0996 10.4486 14.0996 9.39922V6.59922Z"/>
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M12.6464 27.6455C12.8417 27.4502 13.1583 27.4502 13.3536 27.6455L16 30.2919L18.6464 27.6455C18.8417 27.4502 19.1583 27.4502 19.3536 27.6455C19.5488 27.8407 19.5488 28.1573 19.3536 28.3526L16 31.7061L12.6464 28.3526C12.4512 28.1573 12.4512 27.8407 12.6464 27.6455Z"/>
            </svg>
        </button>
    </div>
</section>
