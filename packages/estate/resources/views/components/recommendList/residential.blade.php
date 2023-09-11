<section class="section j-animation__section">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <div class="j-animation__header">
                    <h2>{{ $title ?? trans('kelnik-estate::front.components.recommendList.title') }}</h2>
                </div>
                <div class="similar-flats">
                    <div class="similar-flats__container j-animation__row" data-items="320:2,670:3">
                        @foreach($list as $el)
                            @php
                                /** @var \Kelnik\Estate\Models\Premises $el */
                                $title = $el->typeShortTitle ?? $el->title;
                                $priceVisible = $el->price_is_visible && $el->price_total;
                                $image = $el->imagePlanDefault;
                                $isDefaultImage = true;

                                if ($el->relationLoaded('planoplan') && $el->planoplan->isAvailable()) {
                                    $isDefaultImage = false;
                                    $image = $el->planoplan->widget->plan();
                                } elseif ($el->relationLoaded('imageList') || $el->relationLoaded('imagePlan')) {
                                    $isDefaultImage = false;
                                    $image = $el->relationLoaded('imageList') ? $el->imageList->url() : $el->imagePlan->url();
                                }
                            @endphp
                            <a href="{!! $el->url !!}" target="_blank" class="similar-flat-card j-animation__row-item">
                                <div class="similar-flat-card__container">
                                    <div class="similar-flat-card__plan">
                                        <img src="{!! $image !!}" alt="{{ $el->typeShortTitle }}">
                                        @if(!$isDefaultImage)
                                            <div class="flat__controls">
                                                <button class="flat__fullscreen-button j-popup"
                                                        data-gallery="true"
                                                        data-slider="p-sim-{{ $el->getKey() }}"
                                                        data-src="{!! $image !!}"
                                                        data-alt="{{ $el->typeShortTitle }}"
                                                        data-caption="{{ $el->typeShortTitle }}"
                                                        aria-label="{{ trans('kelnik-estate::front.components.recommendList.plan.openInFullScreen') }}">
                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                                         xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z"></path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="similar-flat-card__details">
                                        <div class="similar-flat-card__title">
                                            <ul class="similar-flat-card__list">
                                                <li class="similar-flat-card__list-item">{{ $title }}</li>
                                                <li class="similar-flat-card__list-item">{{ trans('kelnik-estate::front.components.recommendList.properties.area', ['value' => $el->area_total]) }}</li>
                                            </ul>
                                        </div>
                                        <div class="similar-flat-card__information">
                                            <ul class="similar-flat-card__list">
                                                @if($el->floor->relationLoaded('building'))
                                                    <li class="similar-flat-card__list-item">{{ trans('kelnik-estate::front.components.recommendList.building') }} {{ $el->floor->building->title }}</li>
                                                @endif
                                                @if($el->relationLoaded('section'))
                                                    <li class="similar-flat-card__list-item">{{ trans('kelnik-estate::front.components.recommendList.section') }} {{ $el->section->title }}</li>
                                                @endif
                                                <li class="similar-flat-card__list-item">
                                                    <span>{{ trans('kelnik-estate::front.components.recommendList.floor') }} {{ $el->floor->title }}</span>
                                                    @if($el->floor_max)
                                                        <span>{{ trans('kelnik-estate::front.components.recommendList.maxFloor', ['value' => $el->floor_max]) }}</span>
                                                    @endif
                                                </li>
                                            </ul>
                                            <div class="similar-flat-card__prices">
                                                @if(!$priceVisible && $el->status->additional_text)
                                                    <div class="similar-flat-card__price">{{ $el->status->additional_text }}</div>
                                                @elseif($priceVisible && $el->price_sale)
                                                    <div class="similar-flat-card__price action-price">{{ trans('kelnik-estate::front.components.recommendList.properties.price', ['value' => number_format($el->price_sale, 0, ',', ' ')]) }}</div>
                                                    <div class="action-price__action-wrapper">
                                                        <div class="action-price__action">
                                                            <span class="action-price__action-value">-{{ trans('kelnik-estate::front.components.recommendList.properties.price', ['value' => number_format($el->price_total - $el->price_sale, 0, ',', ' ')]) }}</span>
                                                        </div>
                                                        <div class="action-price__basic-price">{{ trans('kelnik-estate::front.components.recommendList.properties.price', ['value' => number_format($el->price_total, 0, ',', ' ')]) }}</div>
                                                    </div>
                                                @elseif($priceVisible)
                                                    <div class="similar-flat-card__price">{{ trans('kelnik-estate::front.components.recommendList.properties.price', ['value' => number_format($el->price_total, 0, ',', ' ')]) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        @if($el->relationLoaded('features') && $el->features->count())
                                            <div class="similar-flat-card__labels">
                                                <ul class="flat__specials-list">
                                                    @foreach($el->features as $feature)
                                                        <li class="flat__specials-detail">{{ $feature->full_title }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
