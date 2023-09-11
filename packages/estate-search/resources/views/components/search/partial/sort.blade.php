<div class="parametric-result__actions">
    <div class="parametric-result__sort">
        @php
            $sortTitle = null;
            $directions = [
                \Kelnik\EstateSearch\Models\Orders\Contracts\AbstractOrder::DIRECTION_ASC,
                \Kelnik\EstateSearch\Models\Orders\Contracts\AbstractOrder::DIRECTION_DESC
            ];

            foreach ($sortOrder as $el) {
                foreach ($directions as $direction) {
                    if ($el->isSelectedWithDirection($direction)) {
                        $sortTitle = $el->getTitle($direction);
                        break;
                    }
                }
            }
        @endphp
        <div class="custom-select j-parametric-result__sort parametric-result__sort-select is-single" data-placeholder="{{ $sortTitle }}">
            <div class="custom-select__input j-custom-select__input">
                <span class="custom-select__input-text j-custom-select__value">{{ $sortTitle }}</span>
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
                        @foreach($sortOrder as $el)
                            @foreach($directions as $direction)
                                <div class="custom-select__item">
                                    <div class="checkbox custom-select__checkbox">
                                        <input id="checkbox-sort-{{ $el->getName() }}-{{ $direction }}"
                                               type="radio"
                                               class="checkbox__input j-custom-select__checkbox"
                                               name="sort" value="{{ $el->getName() }}-{{ $direction }}"
                                               form="parametric-form"
                                               data-text="{{ $el->getTitle($direction) }}"
                                               @if($el->isSelectedWithDirection($direction)) checked @endif >
                                        <label for="checkbox-sort-{{ $el->getName() }}-{{ $direction }}" class="checkbox__label">
                                            <span class="checkbox__icon checkbox__icon_type_radio">
                                                <svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.5 5.53719L5.38235 9.36328L13.5 1.36328" stroke-linecap="round" />
                                                </svg>
                                            </span>
                                            <span class="checkbox__text">{{ $el->getTitle($direction) }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <div class="custom-select__overlay j-custom-select__close"></div>
            </div>
        </div>
    </div>
    <div class="parametric-result__views">
        <button type="button" class="button-round button-round_theme_green-icon parametric-result__view is-active j-parametric-result__view" aria-label="Вид карточек" data-view="card">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="button-round__icon">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8 6H6V10.8H10.8V6ZM13.2 6H18V10.8H13.2V6ZM6 13.2H10.8V18H6V13.2ZM18 13.2H13.2V18H18V13.2Z" />
            </svg>
        </button>
        <button type="button" class="button-round button-round_theme_green-icon parametric-result__view j-parametric-result__view" aria-label="Вид таблицы" data-view="table">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="button-round__icon">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6 7H18V9H6V7ZM6 11H18V13H6V11ZM18 17H6V15H18V17Z" />
            </svg>
        </button>
    </div>
</div>
