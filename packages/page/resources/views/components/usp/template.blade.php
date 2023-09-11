<section class="section @if(!empty($multiSlider)) section_theme_portrait-utp @endif j-animation__section @isset($margin['top']) section_top_indent-{!! $margin['top'] !!}@endisset @isset($margin['bottom']) section_bottom_indent-{!! $margin['bottom'] !!}@endisset @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid @if(!empty($textOnLeft)) grid_theme_reverse @endif">
        @if(empty($iconPath) && (empty($slider) || $slider->isEmpty())) <div class="grid__common">@endif
        <div class="grid__row">
            <div class="grid__text">
                <div class="text-content">
                    @if(!empty($iconPath))
                        <div class="text-content__icon j-animation__item"><img src="{{ $iconPath }}" width="48" height="48" alt="{{ $title ?? '' }}" /></div>
                    @endif
                    <div class="text-content__main">
                        <div class="text-content__title j-animation__item"><h3>{{ $title ?? '' }}</h3></div>
                        <div class="text-content__text j-animation__content">
                            {!! $text !!}
                            @if(!empty($button['text']))
                                <div class="text-content__button j-animation__content-item"><a href="{!! $button['link'] !!}" class="button">{{ $button['text'] }}</a></div>
                            @endif
                            @if(!empty($options))
                                 <ul>
                                     @foreach($options as $option)
                                        <li>{{ $option['title'] }}</li>
                                     @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty($multiSlider)
                @include('kelnik-page::components.usp.gallery-single')
            @else
                @include('kelnik-page::components.usp.gallery-multi')
            @endempty
        </div>
        @if(empty($iconPath) && (empty($slider) || $slider->isEmpty())) </div>@endif
    </div>
</section>
