<div class="parametric-filter__item parametric-filter__item_size_big">
    <div class="custom-select   custom-select_with_popup j-parametric-select-popup" data-placeholder="Все варианты" data-filter="{{ $baseFilter['name'] }}">
        <div class="custom-select__label">{{ $baseFilter['title'] }}</div>
        <div class="custom-select__input j-parametric-select-popup__input">
            <span class="custom-select__input-text j-parametric-select-popup__value">Все варианты</span>
            <span class="custom-select__input-icon">
                <svg width="24" height="24" class="custom-select__icon">
                    <use xlink:href="/webicons/sprite.svg#i-plus"></use>
                </svg>
            </span>
        </div>
        <div class="parametric-filter__popup j-parametric-select-popup__content">
            <div class="parametric-filter__popup-inner">
                <button type="button" aria-label="Закрыть" class="parametric-filter__popup-close j-parametric-select-popup__close"></button>
                <div class="parametric-filter__popup-wrapper j-parametric-select-popup__wrapper">
                    <div class="parametric-filter__popup-row parametric-filter__popup-row_with_options">
                        @foreach($baseFilter['values'] as $val)
                            <div class="parametric-filter__checkbox">
                                <input id="{{ $baseFilter['name'] }}_{{ $val['id'] }}"
                                       name="{{ $baseFilter['name'] }}[{{ $val['id'] }}]"
                                       value="{{ $val['id'] }}"
                                       data-text="{{ $val['title'] }}"
                                       type="checkbox"
                                       class="parametric-filter__checkbox-input j-parametric-select-popup__checkbox"
                                       @if(in_array($val['id'], $curFilter['selected'])) checked @endif
                                       @if(!isset($curFilter['values'][$val['id']])) disabled @endif >
                                <label for="{{ $baseFilter['name'] }}_{{ $val['id'] }}" class="parametric-filter__checkbox-label">
                                    @if(!empty($val['icon']['url']))
                                        <span class="parametric-filter__checkbox-label-icon">
                                            <img src="{{ $val['icon']['url'] }}" width="48" height="48" alt="{{ $val['title'] }}">
                                        </span>
                                    @endif
                                    <span class="parametric-filter__checkbox-label-text">{{ $val['title'] }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="parametric-filter__popup-row">
                        <button type="button" class="button button_theme_small button_hover_white j-parametric-select-popup__close">
                            <span class="button__inner">
                                <span class="button__text j-parametric-select-popup__count" data-suffix="особенность|особенности|особенностей">Выбрать</span>
                            </span>
                        </button>
                        <button type="button" class="button button_theme_small button_theme_border-green j-parametric-select-popup__reset">
                            <span class="button__inner">
                                <span class="button__text">Сбросить</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="parametric-filter__popup-overlay j-parametric-select-popup__close"></div>
        </div>
    </div>
</div>
