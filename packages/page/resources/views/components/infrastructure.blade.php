<section class="section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__common grid__common_size_wide">
            <div class="grid__row">
                <div class="grid__caption">
                    <h3>{{ $title }}</h3>
                    {!! $text ?? '' !!}
                    @if(!empty($legend))
                        <ul class="legend">
                            @foreach($legend as $el)
                                <li class="legend__item">
                                    <div class="legend__icon">
                                        @if(!empty($el['iconPath']))
                                            <img src="{{ $el['iconPath'] }}" alt="{{ $el['title'] ?? '' }}" width="28" height="28">
                                        @elseif(!empty($el['iconBody']))
                                            {!! $el['iconBody'] !!}
                                        @endif
                                    </div>
                                    <p class="legend__text">{{ $el['title'] ?? '' }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                @if(!empty($plan))
                    <div class="grid__figure">{!! $plan !!}</div>
                @endif
            </div>
        </div>
    </div>
</section>
