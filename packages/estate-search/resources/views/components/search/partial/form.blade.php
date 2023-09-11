<div class="parametric-filter j-parametric-filter">
    <button type="button" class="button button_theme_small parametric-filter__open-button j-parametric-filter__toggle">
        <span class="button__inner">
            <span class="button__text">Фильтры</span>
        </span>
    </button>
    <div class="parametric-filter__content j-parametric-filter__content">
        <button type="button" aria-label="Закрыть фильтры" class="parametric-filter__close j-parametric-filter__toggle"></button>
        <div class="parametric-filter__title">Фильтры</div>
        <form id="parametric-form"
              action="{!! $url !!}"
              method="post"
              class="parametric-filter__form j-parametric-filter__form">
            <div class="parametric-filter__row parametric-filter__row_with_items">
                @includeWhen(
                    $baseBorders->has('building'),
                    'kelnik-estate-search::components.search.partial.form-filters.building',
                    [
                        'baseFilter' => $baseBorders->get('building'),
                        'curFilter' => $currentBorders->get('building')
                    ]
                )

                @includeWhen(
                    $baseBorders->has('stype'),
                    'kelnik-estate-search::components.search.partial.form-filters.type',
                    [
                        'baseFilter' => $baseBorders->get('stype'),
                        'curFilter' => $currentBorders->get('stype')
                    ]
                )

                @includeWhen(
                    $baseBorders->has('area'),
                    'kelnik-estate-search::components.search.partial.form-filters.area',
                    [
                        'baseFilter' => $baseBorders->get('area'),
                        'curFilter' => $currentBorders->get('area')
                    ]
                )

                @includeWhen(
                    $baseBorders->has('price'),
                    'kelnik-estate-search::components.search.partial.form-filters.price',
                    [
                        'baseFilter' => $baseBorders->get('price'),
                        'curFilter' => $currentBorders->get('price')
                    ]
                )

                @includeWhen(
                    $baseBorders->has('floor'),
                    'kelnik-estate-search::components.search.partial.form-filters.floor',
                    [
                        'baseFilter' => $baseBorders->get('floor'),
                        'curFilter' => $currentBorders->get('floor')
                    ]
                )

                @includeWhen(
                   $baseBorders->has('completion'),
                   'kelnik-estate-search::components.search.partial.form-filters.completion',
                   [
                       'baseFilter' => $baseBorders->get('completion'),
                       'curFilter' => $currentBorders->get('completion')
                   ]
                )

                @includeWhen(
                   $baseBorders->has('state'),
                   'kelnik-estate-search::components.search.partial.form-filters.state',
                   [
                       'baseFilter' => $baseBorders->get('state'),
                       'curFilter' => $currentBorders->get('state')
                   ]
                )

                @includeWhen(
                   $baseBorders->has('feature'),
                   'kelnik-estate-search::components.search.partial.form-filters.feature',
                   [
                       'baseFilter' => $baseBorders->get('feature'),
                       'curFilter' => $currentBorders->get('feature')
                   ]
               )
            </div>
            <div class="parametric-filter__row">
                <button type="submit" class="button button_theme_beige button_theme_small parametric-filter__submit j-parametric-filter__submit @if(!$count) disabled @endif">
                    <span class="button__inner">
                        @if(!$count)
                            <span class="button__text j-parametric-filter__submit-text">Вариантов не найдено</span>
                        @else
                            <span class="button__text j-parametric-filter__submit-text">Показать {{ $count }} {{ trans_choice('kelnik-estate-search::front.form.variants', $count) }}</span>
                        @endif
                    </span>
                </button>
                <button type="reset" class="button button_theme_small button_theme_stroke parametric-filter__reset j-parametric-filter__reset">
                    <span class="button__inner">
                        <span class="button__text">Очистить фильтр</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
