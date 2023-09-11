<div class="grid__content-section">
    <div class="details">
        @foreach($variants as $variant)
            <details class="j-details j-animation__item" @if($loop->first && $openFirstVariant) open=""@endif>
                <summary>
                    <span class="summary__icon"></span>
                    <span class="summary__text">{{ $variant['title'] ?? '' }}</span>
                </summary>
                <div class="details__content j-details__content">
                    <div class="grid__row">
                        @if(!empty($variant['text']))
                            <div class="grid__content">{!! $variant['text'] !!}</div>
                        @endif
                        @if(!empty($factoidText) || !empty($button['active']))
                            <div class="grid__factoid">
                                <div class="factoids factoids_size_small">
                                    <div class="factoid">
                                        <div class="factoid__text">
                                            {!! $factoidText ?? '' !!}
                                            @if(!empty($callbackForm))
                                                <x-kelnik-form :params="$callbackForm" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @includeWhen(
                        $banks->isNotEmpty()
                        && isset($variant['showBanks'])
                        && in_array($variant['showBanks'], [\Kelnik\Mortgage\View\Components\HowToBuy\HowToBuy::BANKS_VIEW_LIST, \Kelnik\Mortgage\View\Components\HowToBuy\HowToBuy::BANKS_VIEW_CALC], true),
                        'kelnik-mortgage::components.howToBuy.partials.banks-' . $variant['showBanks'],
                        [
                            'banks' => $banks,
                            'calc' => $variant['showBanks'] === \Kelnik\Mortgage\View\Components\HowToBuy\HowToBuy::BANKS_VIEW_CALC ? ($variant['calc'] ?? []) : null,
                            'calcData' => $calcData ?? []
                        ]
                    )
                </div>
            </details>
        @endforeach
    </div>
</div>
