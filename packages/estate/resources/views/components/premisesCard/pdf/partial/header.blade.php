<header class="pdf-header">
    <div class="pdf-header__logo">
        @if(!empty($logo['dark']))
            <img src="{!! $logo['dark'] !!}" alt="{{ $complex ?? '' }}">
        @endif
    </div>
    <div class="pdf-header__contacts">
        @if($phones)
            <div class="pdf-header__contacts-item">
                <div class="pdf-header__contacts-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.phone') }}</div>
                @foreach($phones as $el)
                    <span>{{ $el['value'] }}</span>
                @endforeach
            </div>
        @endif
        @if($schedule)
            <div class="pdf-header__contacts-item">
                <div class="pdf-header__contacts-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.schedule') }}</div>
                @foreach($schedule as $el)
                    <span>{{ $el['value'] }}</span>
                @endforeach
            </div>
        @endif
        <div class="pdf-header__contacts-item">
            <div class="pdf-header__contacts-type">{{ trans('kelnik-estate::front.components.premisesCard.pdf.site') }}</div>
            <span>{{ $host ?? '' }}</span>
        </div>
    </div>
</header>
