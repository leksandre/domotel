<section class="section j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <h2 class="j-animation__header">{{ $title }}</h2>
                <div class="grid__wrapper">
                    <div class="grid__row">
                        <div class="grid__content j-animation__content">{!! $text ?? '' !!}</div>
                        @includeWhen(
                            !empty($variants),
                            'kelnik-mortgage::components.howToBuy.partials.variants',
                            [
                                'openFirstVariant' => $openFirstVariant ?? false,
                                'variants' => $variants,
                                'banks' => $banks ?? [],
                                'showRange' => $showRange ?? false,
                                'calcData' => $calcData ?? []
                            ]
                        )
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
