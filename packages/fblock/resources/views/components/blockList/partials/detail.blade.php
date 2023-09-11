<div class="flat-block-card__details">
    <div class="flat-block-card__title">{{ $el->title }}</div>
    <div class="flat-block-card__information">
        <div class="flat-block-card__info">
            <div class="flat-block-card__info-title">{{ __('kelnik-fblock::front.components.blockList.area') }}</div>
            <div class="flat-block-card__info-divider"></div>
            <div class="flat-block-card__info-value">{!! $el->area !!}</div>
        </div>
        <div class="flat-block-card__info">
            <div class="flat-block-card__info-title">{{ __('kelnik-fblock::front.components.blockList.floors') }}</div>
            <div class="flat-block-card__info-divider"></div>
            <div class="flat-block-card__info-value">{!! $el->floor !!}</div>
        </div>
        <div class="flat-block-card__info">
            <div class="flat-block-card__info-title">{{ __('kelnik-fblock::front.components.blockList.price') }}</div>
            <div class="flat-block-card__info-divider"></div>
            <div class="flat-block-card__info-value">{!! $el->price !!}</div>
        </div>
    </div>
    @if($el->features)
        <div class="flat-block-card__labels">
            <ul class="flat__specials-list">
                @foreach($el->features as $feature)
                    <li class="flat__specials-detail">{{ $feature }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if($el->callbackForm)
        @php
            $el->callbackForm->templateData['button_class'] = $formClass ?? 'j-popup-callback'
        @endphp
        <x-kelnik-form :params="$el->callbackForm" />
    @endif
</div>
