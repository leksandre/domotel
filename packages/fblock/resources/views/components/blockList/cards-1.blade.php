<section class="section j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="flats-block">
        @include('kelnik-fblock::components.blockList.partials.content')
        @if(!empty($blocks) && $blocks->isNotEmpty())
            <div class="flats-block__gallery">
                <div class="grid">
                    <div class="grid__row">
                        <div class="grid__common">
                            <div class="grid__gallery">
                                <div class="flats-block__wrapper">
                                    <div class="flats-block__mobile-wrapper">
                                        @include(
                                            'kelnik-fblock::components.blockList.partials.card',
                                            [
                                                'pcId' => $pageComponent,
                                                'blocks' => $blocks,
                                                'suffix' => 'm',
                                                'cardCssClass' => 'flat-block-card_theme_two-cards',
                                                'animationParam' => ',1280:2',
                                                'showPop' => true,
                                                'colors' => $colors ?? []
                                            ]
                                        )
                                    </div>
                                    <div class="flats-block__desktop-wrapper j-flats-block-desktop">
                                        <div class="flats-block__top-desktop-wrapper">
                                            @include(
                                                'kelnik-fblock::components.blockList.partials.card',
                                                [
                                                    'pcId' => $pageComponent,
                                                    'blocks' => $blocks->shift(2),
                                                    'suffix' => 'd',
                                                    'cardCssClass' => 'flat-block-card_theme_two-cards',
                                                    'animationParam' => ',1280:2',
                                                    'showPop' => true,
                                                    'colors' => $colors ?? []
                                                ]
                                            )
                                        </div>
                                        @if($blocks->count())
                                            <div class="flats-block__bottom-desktop-wrapper">
                                                <div class="accordion j-accordion accordion_theme_reverse"
                                                     data-change-title="true"
                                                     data-initial-title="{{ trans('kelnik-fblock::front.components.blockList.show') }}"
                                                     data-alternate-title="{{ trans('kelnik-fblock::front.components.blockList.hide') }}">
                                                    <header class="accordion__header j-accordion-header">
                                                        <button class="accordion__title button j-accordion-title"></button>
                                                    </header>
                                                    <div class="accordion__content-wrapper j-accordion-content-outer">
                                                        <div class="accordion__content j-accordion-content">
                                                            @include(
                                                                'kelnik-fblock::components.blockList.partials.card',
                                                                [
                                                                    'pcId' => $pageComponent,
                                                                    'blocks' => $blocks,
                                                                    'suffix' => 'd',
                                                                    'cardCssClass' => 'flat-block-card_theme_two-cards',
                                                                    'animationParam' => ',1280:2',
                                                                    'showPop' => true,
                                                                    'colors' => $colors ?? []
                                                                ]
                                                            )
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
