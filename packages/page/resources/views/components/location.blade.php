<section class="section location j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <h2 class="j-animation__header">{{ $title ?? '' }}</h2>
                @if(!empty($usp))
                    <ul class="location-adv j-animation__row">
                        @foreach($usp as $el)
                            <li class="location-adv__item j-animation__row-item">
                                @if(!empty($el['iconPath']) || !empty($el['iconBody']) || !empty($el['icon']))
                                    <div class="location-adv__icon">
                                        @if(!empty($el['iconPath']))
                                            <img src="{{ $el['iconPath'] }}" alt="{{ $el['title'] ?? '' }}" width="48" height="48">
                                        @elseif(!empty($el['iconBody']))
                                            {!! $el['iconBody'] !!}
                                        @elseif($el['icon'] instanceof \Orchid\Attachment\Models\Attachment)
                                            <img src="{{ $el['icon']->url }}" alt="{{ $el['title'] ?? '' }}" width="48" height="48">
                                        @endif
                                    </div>
                                @endif
                                <div class="location-adv__info"><p>{{ $el['title'] ?? '' }}</p></div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        @if(!empty($map['json']))
            <div class="grid__row">
                <div class="grid__map j-animation__item">
                    <div class="yandex-map j-location-map" data-json="{{ $map['json'] ?? '' }}"><div class="yandex-map__container"><div id="locationMap" class="yandex-map__base j-yandex-map-base"></div></div></div>
                    @if(!empty($map['route']['active']))
                        <div class="grid__map-route">
                            <a href="{!! $map['route']['link'] ?? '/' !!}" target="_blank" rel="noopener noreferrer" class="yandex-map__route">
                                {{ $map['route']['title'] ?? trans('kelnik-page::front.components.location.makeRoute') }}
                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 7.2V10.8C10 11.4624 9.4624 12 8.8 12H2.2C1.5376 12 1 11.4624 1 10.8V4.2C1 3.5376 1.5376 3 2.2 3H5.8" stroke="#0B1739" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8 1H12V5" stroke="#0B1739" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5 8L12 1" stroke="#0B1739" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    @endif
                    @if(!empty($map['types']))
                        <div class="grid__map-filter-btn">
                            <button class="yandex-map__filter j-location-filter-list-toggle" aria-label="{{ trans('kelnik-page::front.components.location.toggleFilter') }}">
                                <span class="yandex-map__filter-text">{{ trans('kelnik-page::front.components.location.objectsOnMap') }}</span>
                                <span class="yandex-map__filter-icon open">
                                        <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 3H19" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M19 11L1 11" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <circle cx="6" cy="3" r="2" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <circle cx="14" cy="11" r="2" transform="rotate(-180 14 11)" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                <span class="yandex-map__filter-icon close">
                                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2426 1.24264L6 5.48528M6 5.48528L10.2426 9.72792M6 5.48528L1.75736 9.72792M6 5.48528L1.75736 1.24264" stroke="white" stroke-width="2" stroke-linecap="round" /></svg>
                                </span>
                            </button>
                        </div>
                    @endif
                </div>

                @if(!empty($map['types']))
                    <div class="grid__map-location j-location-infrastructure">
                        <div class="map-filter__overlay j-location-filter-list-toggle"></div>
                        <div class="map-filter">
                            <div class="map-filter__head">
                                <button type="button" class="map-filter__head-close j-location-filter-list-toggle"
                                    aria-label="Close filter">
                                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2426 1.24264L6 5.48528M6 5.48528L10.2426 9.72792M6 5.48528L1.75736 9.72792M6 5.48528L1.75736 1.24264" stroke="#0B1739" stroke-width="2" stroke-linecap="round" /></svg>
                                </button>
                            </div>
                            <div class="map-filter__header">
                                <span class="map-filter__title">{{ trans('kelnik-page::front.components.location.objectsOnMap') }}</span>
                                <button type="button" class="map-filter__header-button j-checkbox-toggle">{{ trans('kelnik-page::front.components.location.hideAll') }}</button>
                            </div>
                            <div class="map-filter__content j-map-filter__content">
                                <ul class="map-filter__infrastructure-list j-map-filter">
                                    @foreach($map['types'] as $type)
                                        <li class="map-filter__infrastructure-item">
                                            <label class="map-filter__infrastructure-label">
                                                @if(!empty($type['iconPath']))
                                                    <img src="{{ $type['iconPath'] }}" width="56" height="56" alt="{{ $type['icon']->alt ?: $type['title'] }}">
                                                @elseif(!empty($type['iconBody']))
                                                    {!! $type['iconBody'] !!}
                                                @elseif($type['icon'] instanceof \Orchid\Attachment\Models\Attachment)
                                                    <img src="{{ $type['icon']->url }}" alt="{{ $type['icon']->alt }}">
                                                @endif
                                                {{ $type['title'] }}
                                                <input class="map-filter__checkbox j-map-filter-item" type="checkbox" id="{{ $type['code'] }}" name="{{ $type['code'] }}" data-type="{{ $type['code'] }}" checked>
                                                <span class="map-filter__fake-checkbox">
                                                    <svg width="12" height="9" viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.5 3.72727L4.67647 7L10.5 1" stroke="white" stroke-width="2" stroke-linecap="round" /></svg>
                                                </span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="map-filter__content-close j-location-filter-list-toggle">{{ trans('kelnik-page::front.components.location.close') }}</button>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
