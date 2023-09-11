<div class="grid__content-section">
    <div class="slider slider_theme_banks j-slider-news">
        <div class="slider__wrap j-slides action">
            @foreach($banks as $bank)
                <article class="bank-card">
                    @if($bank->link)
                        <a href="{{ $bank->link }}" target="_blank" rel="noopener noreferrer" class="bank-card__bank">
                    @else
                        <div class="bank-card__bank">
                    @endif
                        @if($bank->logo->exists)
                            <img loading="lazy" data-src="{{ $bank->logoResizedPath ?: $bank->logo->url }}" alt="{{ $bank->title }}" width="48" height="48" class="bank-card__logo">
                        @endif
                        <div class="bank-card__title">{{ $bank->title }}</div>
                    @if($bank->link)
                        </a>
                    @else
                        </div>
                    @endif

                    @php
                        foreach (['Rate', 'PaymentPercent', 'Time'] as $name) {
                            foreach (['min', 'max'] as $prefix) {
                                $varName = $prefix . $name;
                                ${$varName} = \Kelnik\Core\Helpers\NumberHelper::normalizeFloat($bank->programsParamRange->get($varName));
                            }
                        }
                    @endphp
                    <ul class="bank-card__conditions">
                        <li class="bank-card__condition">
                            <span class="bank-card__key">{{ trans('kelnik-mortgage::front.components.howToBuy.rate') }}</span>
                            @if($showRange && $minRate !== $maxRate)
                                <span class="bank-card__value">{{ $minRate }} - {{ $maxRate }} %</span>
                            @else
                                <span class="bank-card__value">{{ trans('kelnik-mortgage::front.components.howToBuy.from') }} {{ $minRate }} %</span>
                            @endif
                        </li>
                        <li class="bank-card__condition">
                            <span class="bank-card__key">{{ trans('kelnik-mortgage::front.components.howToBuy.firstPayment') }}</span>
                            @if($showRange && $minPaymentPercent !== $maxPaymentPercent)
                                <span class="bank-card__value">{{ $minPaymentPercent }} - {{ $maxPaymentPercent }} %</span>
                            @else
                                <span class="bank-card__value">{{ trans('kelnik-mortgage::front.components.howToBuy.from') }} {{ $minPaymentPercent }} %</span>
                            @endif
                        </li>
                        <li class="bank-card__condition">
                            @if($showRange && $minTime !== $maxTime)
                                <span class="bank-card__key">{{ trans('kelnik-mortgage::front.components.howToBuy.termYears') }}</span>
                                <span class="bank-card__value">{{ $minTime }} - {{ $maxTime }}</span>
                            @else
                                <span class="bank-card__key">{{ trans('kelnik-mortgage::front.components.howToBuy.termTo') }}</span>
                                <span class="bank-card__value">{{ $maxTime . ' ' . trans_choice('kelnik-mortgage::front.components.howToBuy.termToPlural', $maxTime) }}</span>
                            @endif
                        </li>
                    </ul>
                    <div class="bank-card__footer">{!! $bank->description !!}</div>
                </article>
            @endforeach
        </div>
    </div>
</div>
