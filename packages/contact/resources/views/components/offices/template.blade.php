<section class="section section_theme_contacts j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <div class="grid__wrapper grid__wrapper_theme_contacts">
                    <h2 class="j-animation__header">{{ $title ?? '' }}</h2>
                    <x-kelnik-contact-social-links />
                </div>
                @if(!empty($list))
                    <div class="grid__wrapper">
                        <div class="grid__row">
                            <div class="contacts j-animation__row" data-items="320:1,670:2">
                                @foreach($list as $office)
                                    <div class="contacts__item j-animation__row-item">
                                        <div class="contacts__icon">
                                            <svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 6.58C0 3.22736 2.69147 0.5 6 0.5C9.30853 0.5 12 3.22736 12 6.58C12 10.5328 7.25288 15.3573 6.36824 16.2563C6.29809 16.3276 6.25223 16.3742 6.23495 16.3938C6.17526 16.4613 6.08968 16.5 6 16.5C5.91032 16.5 5.82474 16.4613 5.76505 16.3938C5.74782 16.3743 5.70228 16.328 5.63266 16.2573C4.75028 15.3608 0 10.5342 0 6.58ZM3 6.5C3 4.846 4.346 3.5 6 3.5C7.65433 3.5 9 4.846 9 6.5C9 8.154 7.65433 9.5 6 9.5C4.346 9.5 3 8.154 3 6.5Z" />
                                            </svg>
                                        </div>
                                        <div class="contacts__content" itemscope="" itemtype="http://schema.org/RealEstateAgent">
                                            <div class="contacts__title" itemprop="name">{{ $office->title }}</div>
                                            @if($office->address)
                                                <div class="contacts__info not-wrap" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
                                                    @if($office->region)<span itemprop="addressRegion">{{ $office->region }}</span>,@endif
                                                    @if($office->city)<span itemprop="addressLocality">{{ $office->city }}</span>,@endif
                                                    @if($office->street)<span itemprop="streetAddress">{{ $office->street }}</span>@endif
                                                </div>
                                            @endif

                                            @if($office->phone)
                                                <div class="contacts__info" itemprop="telephone"><a href="tel:{{ $office->phoneLink }}" class="contacts__info_phone_contact">{{ $office->phone }}</a></div>
                                            @endif

                                            @if(!empty($office->schedule))
                                                <div class="contacts__info">
                                                    @foreach($office->schedule as $el)
                                                        <span>{{ $el['day'] ?? '' }}: {{ $el['time'] ?? '' }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($office->email)
                                                <a href="mailto:{{ $office->email }}" class="contacts__info">{{ $office->email }}</a>
                                            @endif

                                            @if($office->route_link)
                                                <div class="contacts__info">
                                                    <a href="{{ $office->route_link }}" class="link link_theme_external">
                                                        <span class="link__text">{{ trans('kelnik-contact::front.routeLink') }}</span>
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g opacity="0.5">
                                                                <path d="M11 10V12C11 12.5523 10.5523 13 10 13H4C3.44772 13 3 12.5523 3 12V6C3 5.44772 3.44772 5 4 5H6" stroke="#0B1739" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path d="M6.27271 9.72727L12 4M12 4H9M12 4V7" stroke="#0B1739" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </g>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @if(!empty($mapJson))
            <div class="grid__row">
                <div class="grid__map j-animation__item">
                    <div class="yandex-map j-contacts-map" data-json="{{ $mapJson }}">
                        <div class="yandex-map__container">
                            <div id="contactsMap" class="yandex-map__base j-yandex-map-base"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
