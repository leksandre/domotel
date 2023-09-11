<section class="section section_theme_grey j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <h2 class="j-animation__header">{{ $title }}</h2>
                <div class="grid__wrapper">
                    <div class="grid__row">
                        <div class="grid__content-section">
                            <div class="details">
                                @foreach($list as $category)
                                    <details class="j-details j-animation__item">
                                        <summary>
                                            <span class="summary__icon"></span>
                                            <span class="summary__text">{{ $category->title }}</span>
                                            <span class="summary__count">{{ $category->elements->count() }}</span>
                                        </summary>
                                        <div class="details__content j-details__content">
                                            <div class="grid__content-section">
                                                <ul class="documents__list">
                                                    @foreach($category->elements as $element)
                                                        <li class="document">
                                                            <div class="document__icon">
                                                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M8 40H31C33.2091 40 35 38.2091 35 36V14.6569C35 13.596 34.5786 12.5786 33.8284 11.8284L23.1716 1.17157C22.4214 0.421429 21.404 0 20.3431 0H8C5.79086 0 4 1.79086 4 4V36C4 38.2091 5.79086 40 8 40Z" fill="#3EB57C" />
                                                                    <path d="M19 0C21.4 0 22 2 22 3V9C22 11.2091 23.7909 13 26 13H32C34.4 13 35 15 35 16V14.6569C35 13.596 34.5786 12.5786 33.8284 11.8284L23.1716 1.17157C22.4214 0.421427 21.404 0 20.3431 0H19Z" fill="#95D0A1" />
                                                                    <path d="M19.5 18V28.5M19.5 28.5L16 25M19.5 28.5L23 25M23 32.5H19.5H16" stroke="white" />
                                                                </svg>
                                                            </div>
                                                            <div class="document__description">
                                                                <a class="document__title" href="{{ $element->attachmentUrl }}" target="_blank"><span>{{ $element->title }}</span></a>
                                                                <div class="document__info">
                                                                            <span class="document__size">
                                                                                {{ $element->attachmentExtension }}, <span>{{ $element->attachmentSizeFormatted }}</span>
                                                                            </span>
                                                                    <span class="document__author">{{ $element->author }}</span>
                                                                    <span class="document__date">{{ $element->publishDateFormatted }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
