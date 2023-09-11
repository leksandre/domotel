<section class="section j-animation__section @isset($margin['top']) section_top_indent-{!! $margin['top'] !!}@endisset @isset($margin['bottom']) section_bottom_indent-{!! $margin['bottom'] !!}@endisset" @if(!empty($alias))id="{{ $alias }}"@endif>
    <div class="grid @if(!empty($textOnRight)) grid_theme_reverse @endif">
        <div class="grid__common">
            <h2 class="j-animation__header">{{ $title }}</h2>
            <div class="grid__wrapper">
                <div class="grid__row">
                    <div class="grid__content j-animation__content">
                        {!! $text !!}
                        @if(!empty($button['text']))
                            <div class="grid__button-wrapper j-animation__content-item"><a href="{!! $button['link'] !!}" class="button">{{ $button['text'] }}</a></div>
                        @endif
                    </div>
                    @if(!empty($factoids))
                        <div class="grid__factoid">
                            <ul class="factoids factoids_theme_marked">
                                @foreach($factoids as $factoid)
                                <li class="factoid j-animation__item">
                                    <strong class="factoid__title">{{ $factoid['title'] }}</strong>
                                    <div class="factoid__text"><p>{{ $factoid['text'] }}</p></div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
