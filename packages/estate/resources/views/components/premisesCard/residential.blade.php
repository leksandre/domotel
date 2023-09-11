<section class="section j-animation__section section_theme_flat @if(!empty($background) && $background === \Kelnik\Estate\View\Components\PremisesCard\PremisesCard::BACKGROUND_COLOR) section_theme_flat-background @endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__flat">
                <div class="flat-card j-tabs-wrap">
                    <div class="flat-card__breadcrumbs">
                        <a href="{!! $backLink !!}" class="button-circle button-circle_theme_gray flat-card__back">
                            <span class="button__icon">
                                <svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 5.00063H2M2 5.00063L6 1M2 5.00063L6 9.00126" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </span>
                        </a>
                        @include('kelnik-estate::components.premisesCard.partial.share')
                        @include('kelnik-estate::components.premisesCard.partial.plan-tabs')
                        @php
                            $title = $element->typeShortTitle ?? $element->title;
                            $priceVisible = $element->price_is_visible && $element->price_total;
                        @endphp
                    </div>
                    <div class="flat-card__content">
                        <div class="flat">
                            @include('kelnik-estate::components.premisesCard.partial.plan')
                            <div class="flat__info">
                                <div class="flat__general">
                                    <div class="flat__title">{{ $title }}</div>
                                    <div class="price flat__prices">
                                        @if(!$priceVisible && $element->status->additional_text)
                                            <div class="price__wrapper">
                                                <div class="price__current">{{ $element->status->additional_text }}</div>
                                            </div>
                                        @elseif($priceVisible && $element->price_sale)
                                            <div class="price__wrapper">
                                                <div class="price__basic">{{ trans('kelnik-estate::front.components.premisesCard.properties.price', ['value' => number_format($element->price_total, 0, ',', ' ')]) }}</div>
                                                <div class="price__current">{{ trans('kelnik-estate::front.components.premisesCard.properties.price', ['value' => number_format($element->price_sale, 0, ',', ' ')]) }}</div>
                                                <div class="price__discount">
                                                    <span class="price__discount-text">-{{ trans('kelnik-estate::front.components.premisesCard.properties.price', ['value' => number_format($element->price_total - $element->price_sale, 0, ',', ' ')]) }}</span>
{{--                                                    <span class="info-pin">--}}
{{--                                                        <svg width="2" height="8" viewBox="0 0 2 8" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--                                                            <rect width="2" height="5" rx="1" fill="white" />--}}
{{--                                                            <rect y="6" width="2" height="2" rx="1" fill="white" />--}}
{{--                                                        </svg>--}}
{{--                                                        <div class="info-pin__tooltip">Текст описывающий скидку</div>--}}
{{--                                                    </span>--}}
                                                </div>
                                            </div>
                                            @include('kelnik-estate::components.premisesCard.partial.mortgage-payment')
                                        @elseif($priceVisible)
                                            <div class="price__wrapper">
                                                <div class="price__current">{{ trans('kelnik-estate::front.components.premisesCard.properties.price', ['value' => number_format($element->price_total, 0, ',', ' ')]) }}</div>
                                            </div>
                                            @include('kelnik-estate::components.premisesCard.partial.mortgage-payment')
                                        @endif
                                    </div>
                                </div>
                                @if(!empty($callbackForm) || !empty($pdfLink))
                                    <div class="flat__callback">
                                        @if(!empty($callbackForm))
                                            <x-kelnik-form :params="$callbackForm" />
                                        @endif
                                        @if(!empty($pdfLink))
                                            <a href="{!! $pdfLink !!}" class="flat__callback-print button button_theme_icon button_theme_left-icon button_theme_white">
                                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4 7.19165C3.86193 7.19165 3.75 7.30358 3.75 7.44165V15.4417C3.75 15.5797 3.86197 15.6917 4 15.6917H6.5V17.1917H4C3.03347 17.1917 2.25 16.4081 2.25 15.4417V7.44165C2.25 6.47516 3.03351 5.69165 4 5.69165H20C20.9665 5.69165 21.75 6.47517 21.75 7.44165V15.4417C21.75 16.4081 20.9666 17.1917 20 17.1917H17.5V15.6917H20C20.138 15.6917 20.25 15.5797 20.25 15.4417V7.44165C20.25 7.30357 20.1381 7.19165 20 7.19165H4Z" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9 3.19165C8.30964 3.19165 7.75 3.75129 7.75 4.44165V5.69165H16.25V4.44165C16.25 3.75129 15.6904 3.19165 15 3.19165H9ZM6.25 4.44165C6.25 2.92287 7.48122 1.69165 9 1.69165H15C16.5188 1.69165 17.75 2.92287 17.75 4.44165V7.19165H6.25V4.44165Z" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.75 13.1917V21.6916L16.25 21.6917V13.1917H7.75ZM6.25 12.9417C6.25 12.2512 6.80971 11.6917 7.5 11.6917H16.5C17.1902 11.6917 17.75 12.2512 17.75 12.9417V21.9417C17.75 22.632 17.1903 23.1917 16.5 23.1917L7.5 23.1916C6.80971 23.1916 6.25 22.6321 6.25 21.9416V12.9417Z" />
                                                    <path d="M16 10.4417C15.4477 10.4417 15 9.99394 15 9.44165C15 8.88937 15.4477 8.44165 16 8.44165C16.5523 8.44165 17 8.88937 17 9.44165C17 9.99394 16.5523 10.4417 16 10.4417Z" />
                                                </svg>
                                                {{ trans('kelnik-estate::front.components.premisesCard.share.printToPdf') }}
                                            </a>
                                        @endif
                                        @if(!empty($vr['active']) && !empty($isVr) && !empty($element->vr_link))
                                            <a href="{!! $element->vr_link !!}" class="button">
                                                <span>{{ $vr['buttonText'] ?? trans('kelnik-estate::front.components.premisesCard.vr.buttonText') }}</span>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <div class="flat__details">
                                    <div class="flat__general-details">
                                        <div class="flat__details-title">{{ trans('kelnik-estate::front.components.premisesCard.properties.title') }}</div>
                                        <ul class="flat__details-list">
                                            <li class="flat__detail flat__detail_accent_detail">
                                                <div class="flat__detail-name">{{ trans('kelnik-estate::front.components.premisesCard.properties.areaTotal') }}</div>
                                                <div class="flat__detail-separator"></div>
                                                <div class="flat__detail-value">{{ trans('kelnik-estate::front.components.premisesCard.properties.area', ['value' => $element->area_total]) }}</div>
                                            </li>
                                            @if($element->area_living)
                                                <li class="flat__detail">
                                                    <div class="flat__detail-name">{{ trans('kelnik-estate::front.components.premisesCard.properties.areaLiving') }}</div>
                                                    <div class="flat__detail-separator"></div>
                                                    <div class="flat__detail-value">{{ trans('kelnik-estate::front.components.premisesCard.properties.area', ['value' => $element->area_living]) }}</div>
                                                </li>
                                            @endif
                                            @if($element->area_kitchen)
                                                <li class="flat__detail">
                                                    <div class="flat__detail-name">{{ trans('kelnik-estate::front.components.premisesCard.properties.areaKitchen') }}</div>
                                                    <div class="flat__detail-separator"></div>
                                                    <div class="flat__detail-value">{{ trans('kelnik-estate::front.components.premisesCard.properties.area', ['value' => $element->area_kitchen]) }}</div>
                                                </li>
                                            @endif
                                            @if($hasBuilding)
                                                <li class="flat__detail">
                                                    <div class="flat__detail-name">{{ trans('kelnik-estate::front.components.premisesCard.building') }}</div>
                                                    <div class="flat__detail-separator"></div>
                                                    <div class="flat__detail-value">{{ $element->floor->building->title }}</div>
                                                </li>
                                            @endif
                                            @if($hasSection)
                                                <li class="flat__detail">
                                                    <div class="flat__detail-name">{{ trans('kelnik-estate::front.components.premisesCard.section') }}</div>
                                                    <div class="flat__detail-separator"></div>
                                                    <div class="flat__detail-value">{{ $element->section->title }}</div>
                                                </li>
                                            @endif
                                            @if($hasFloor)
                                                <li class="flat__detail">
                                                    <div class="flat__detail-name">{{ trans('kelnik-estate::front.components.premisesCard.floor') }}</div>
                                                    <div class="flat__detail-separator"></div>
                                                    <div class="flat__detail-value">{{ $element->floor->title }}@if($element->floor_max) {{ trans('kelnik-estate::front.components.premisesCard.maxFloor', ['value' => $element->floor_max]) }}@endif</div>
                                                </li>
                                            @endif
                                            @if($hasBuilding && $hasCompletion)
                                                <li class="flat__detail">
                                                    <div class="flat__detail-name">{{ trans('kelnik-estate::front.components.premisesCard.completion') }}</div>
                                                    <div class="flat__detail-separator"></div>
                                                    <div class="flat__detail-value">{{ $element->floor->building->completion->title }}</div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    @if($element->additional_properties)
                                        <div class="flat__more-details details">
                                            <details class="j-details j-animation__item">
                                                <summary>
                                                    <span class="flat__more-details-title">{{ trans('kelnik-estate::front.components.premisesCard.additionalProperties') }}</span>
                                                    <span class="flat__more-details-icon">
                                                        <svg width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M9 1L5 5L1 1" stroke-width="2" stroke-linecap="round" />
                                                        </svg>
                                                    </span>
                                                </summary>
                                                <div class="flat__more-details-content j-details__content">
                                                    <ul class="flat__details-list">
                                                        @foreach($element->additional_properties as $property)
                                                            <li class="flat__detail">
                                                                <div class="flat__detail-name">{{ $property['title'] }}</div>
                                                                <div class="flat__detail-separator"></div>
                                                                <div class="flat__detail-value">{{ $property['value'] }}</div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </details>
                                        </div>
                                    @endif
                                </div>
                                @if($element->relationLoaded('features') && $element->features->isNotEmpty())
                                    <div class="flat__specials">
                                        <div class="flat__specials-title">{{ trans('kelnik-estate::front.components.premisesCard.features') }}</div>
                                        <ul class="flat__specials-list">
                                            @foreach($element->features as $feature)
                                                <li class="flat__specials-detail">{{ $feature->full_title }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
