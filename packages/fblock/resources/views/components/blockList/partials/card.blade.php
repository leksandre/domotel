<div class="flats-block__container j-animation__row" data-items="320:2,670:3{!! $animationParam !!}">
    @foreach($blocks as $el)
        <div class="flat-block-card j-animation__row-item {!! $cardCssClass !!}">
            <div class="flat-block-card__content">
                <div class="flat-block-card__plan-wrap">
                    @include('kelnik-fblock::components.blockList.partials.card-plan', ['pcId' => $pcId ?? 0, 'suffix' => $suffix ?? 'd', 'showPop' => $showPop ?? false])
                </div>
                @include('kelnik-fblock::components.blockList.partials.detail')
            </div>
        </div>
    @endforeach
</div>
