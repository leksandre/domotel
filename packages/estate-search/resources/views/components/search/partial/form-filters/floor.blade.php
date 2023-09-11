@php
    $minValue = $curFilter['minValue'] ?? $baseFilter['min'];
    $maxValue = $curFilter['maxValue'] ?? $baseFilter['max'];
@endphp
<div class="parametric-filter__item">
    <div class="range-slider j-range-slider" data-slider='{{ $baseFilter['name'] }}'>
        <div class="range-slider__label">{{ $baseFilter['title'] }}</div>
        <div class="range-slider__inputs">
            <div class="range-slider__input-wrap">
                <span class="range-slider__input-prefix">от </span>
                <input class="range-slider__input j-range-slider__input" type="text" value="{{ $minValue }}" name="{{ $baseFilter['name'] }}[min]" autocomplete="off" data-validate="integer" data-digit="true">
            </div>
            <div class="range-slider__input-wrap">
                <span class="range-slider__input-prefix">по </span>
                <input class="range-slider__input j-range-slider__input" type="text" value="{{ $maxValue }}" name="{{ $baseFilter['name'] }}[max]" autocomplete="off" data-validate="integer" data-digit="true">
            </div>
        </div>
        <div class="range-slider__base">
            <input id="{{ $baseFilter['name'] }}" type="text" class="j-range-slider__base"
                   value=""
                   data-min="{{ $baseFilter['min'] }}"
                   data-max="{{ $baseFilter['max'] }}"
                   data-from="{{ $minValue }}"
                   data-to="{{ $maxValue }}"
                   data-type="double"
                   data-step="1"
                   data-min-interval="2"
                   data-digit="true"
                   data-validate="integer"
                   autocomplete="off" />
        </div>
    </div>
</div>
