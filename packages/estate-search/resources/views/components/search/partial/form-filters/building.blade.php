<div class="parametric-filter__item">
    <div class="custom-select j-parametric-filter__select" data-placeholder="Все" data-filter="{{ $baseFilter['name'] }}" data-suffix="корпус|корпуса|корпусов" data-value-type="summary">
        <div class="custom-select__label">{{ $baseFilter['title'] }}</div>
        <div class="custom-select__input j-custom-select__input">
            <span class="custom-select__input-text j-custom-select__value">Все</span>
            <span class="custom-select__input-icon">
                <svg width="24" height="24" class="custom-select__icon">
                    <use xlink:href="/webicons/sprite.svg#i-arr-dropdown-down"></use>
                </svg>
            </span>
        </div>
        <div class="custom-select__content j-custom-select__content">
            <div class="custom-select__content-inner">
                <button type="button" aria-label="Закрыть" class="custom-select__close j-custom-select__close"></button>
                <div class="custom-select__items j-custom-select__items">
                    @foreach($baseFilter['values'] as $building)
                        <div class="checkbox custom-select__checkbox">
                            <input id="checkbox-{{ $baseFilter['name'] }}_{{ $building['id'] }}"
                                   type="checkbox"
                                   class="checkbox__input j-custom-select__checkbox"
                                   name="{{ $baseFilter['name'] }}[{{ $building['id'] }}]"
                                   value="{{ $building['id'] }}"
                                   data-text="{{ $building['title'] }}"
                                   @if(in_array($building['id'], $curFilter['selected'])) checked @endif
                                   @if(!isset($curFilter['values']['buildings'][$building['id']])) disabled @endif>
                            <label for="checkbox-{{ $baseFilter['name'] }}_{{ $building['id'] }}" class="checkbox__label">
                                <span class="checkbox__icon">
                                    <svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.5 5.53719L5.38235 9.36328L13.5 1.36328" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <span class="checkbox__text">{{ $building['title'] }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="custom-select__overlay j-custom-select__close"></div>
        </div>
    </div>
</div>
