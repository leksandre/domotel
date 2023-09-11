<section class="parametric j-parametric">
    <div class="parametric__head">
        <div class="parametric__head-wrapper">
            <div class="parametric__container">
                <div class="parametric__content">
                    <div class="parametric__head-row">
                        <h1>{{ $title ?? 'Квартиры' }}</h1>
                        <div class="parametric__views">
                            <a href="/search/" class="button button_theme_tabs button_tabs_brand is-active">
                                <span class="button__inner">
                                    <span class="button__text">По параметрам</span>
                                </span>
                            </a>
                            <a href="/visual/" class="button button_theme_tabs button_tabs_brand j-visual__link">
                                <span class="button__inner">
                                    <span class="button__text">На генплане</span>
                                </span>
                            </a>
                        </div>
                    </div>
                    @includeWhen(
                        !empty($form),
                        'kelnik-estate-search::components.search.partial.form',
                        [
                            'baseBorders' => $form->get('baseBorders'),
                            'currentBorders' => $form->get('currentBorders'),
                            'count' => $form->get('count', 0),
                            'url' => $url
                        ]
                    )
                </div>
            </div>
        </div>
    </div>
    <div class="parametric__body">
        <div class="parametric__container">
            <div class="parametric__content">
                <div class="parametric-result j-parametric-result">
                    @php
                        $isHidden = $results->get('items')?->isNotEmpty() ? ' is-hidden' : '';
                    @endphp
                    <div class="parametric-result__no-result j-parametric-result__nothing{{ $isHidden }}">
                        @include('kelnik-estate-search::components.search.partial.no-results')
                    </div>
                    @includeWhen(
                        $results->get('items')?->isNotEmpty(),
                        'kelnik-estate-search::components.search.partial.sort',
                        [
                            'sortOrder' => $results->get('sortOrder')
                        ]
                    )
                    @includeWhen(
                        $results->get('items')?->isNotEmpty(),
                        'kelnik-estate-search::components.search.partial.results',
                        [
                            'items' => $results->get('items'),
                            'more' => $results->get('more')
                        ]
                    )
                </div>
            </div>
        </div>
    </div>
</section>
