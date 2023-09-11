<section class="section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__common">
            <h2 class="j-animation__header">{{ $title }}</h2>
            <div class="grid__wrapper">
                {!! $text ?? '' !!}
                @if(!empty($list))
                    <div class="advantages advantages_wrap_{!! $lineLimit !!}">
                        @foreach($list as $el)
                            <div class="advantage">
                                @if(!empty($el['iconBody']))
                                    <div class="advantage__icon">{!! $el['iconBody'] !!}</div>
                                @elseif(!empty($el['iconPath']))
                                    <div class="advantage__icon"><img src="{{ $el['iconPath'] }}" alt="{{ $el['title'] ?? '' }}"></div>
                                @endif
                                @if(!empty($el['title']))<p class="advantage__title"><b>{{ $el['title'] }}</b></p>@endif
                                @if(!empty($el['text']))<p class="advantage__text">{{ $el['text'] }}</p>@endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
