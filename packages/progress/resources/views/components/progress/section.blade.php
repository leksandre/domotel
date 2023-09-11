<section class="section section_theme_progress j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <h2 class="j-animation__header">{{ $title ?? '' }}</h2>
                <div class="grid__wrapper">
                    <div class="grid__row">
                        <div class="grid__content">
                            @if(!empty($deadlines))
                                <div class="grid__features">
                                    @foreach($deadlines as $el)
                                        <div class="grid__feature j-animation__item">
                                            @if(!empty($el['title']))
                                                <p class="grid__feature-title">{{ $el['title'] }}</p>
                                            @endif
                                            @if(!empty($el['text']))
                                                <p class="grid__feature-text">{{ $el['text'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if(!empty($cameras) && $cameras->isNotEmpty() ?? !empty($buttonText))
                            <div class="grid__factoid j-animation__item">
                                <button type="button" class="button j-popup" data-ajax-online="true" data-ajax="{{ route('kelnik.progress.cameras', !empty($group) ? ['group' => $group] : [], false) }}" data-query="GET" aria-label="{{ $buttonText }}">
                                    <span>{{ $buttonText }}</span>
                                </button>
                            </div>
                        @endif
                        @if(!empty($text))
                            <div class="grid__content j-animation__content">{!! $text !!}</div>
                        @endif
                        @if(!empty($albums) && $albums->isNotEmpty())
                            <div class="grid__content-section">
                                <div class="slider slider_theme_banks j-slider-news j-animation__slider">
                                    <div class="slider__wrap j-slides action">
                                        @foreach($albums as $album)
                                            <article class="progress-card j-popup-slider j-animation__row-item" data-progress="true" data-ajax="{{ route('kelnik.progress.albums', !empty($group) ? ['group' => $group] : [], false) }}" data-query="GET" data-id="{{ $album->getKey() }}">
                                                <div class="progress-card__image">
                                                    @if($album->coverPicture)
                                                        {!! $album->coverPicture !!}
                                                    @elseif($album->coverImage)
                                                        <img src="{{ $album->coverImage }}" alt="{{ $album->title ?? '' }}" width="360" height="202" />
                                                    @endif
                                                </div>
                                                <div class="progress-card__content">
                                                    <div class="progress-card__texts">
                                                        <div class="progress-card__title">{{ $album->title }}</div>
                                                        <p class="progress-card__info">{{ $album->comment }}</p>
                                                    </div>
                                                    <div class="progress-card__counts">
                                                        @php
                                                            $imagesCnt = $album->images->count();
                                                            $videosCnt = $album->videos->count();
                                                        @endphp
                                                        @if($videosCnt)
                                                            <div class="progress-card__count">
                                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M11 3H2C1.44772 3 1 3.44772 1 4V10C1 10.5523 1.44772 11 2 11H11C11.5523 11 12 10.5523 12 10V4C12 3.44772 11.5523 3 11 3Z" />
                                                                    <path d="M14.1463 3.85356L11.3535 6.64646C11.1582 6.84172 11.1582 7.1583 11.3535 7.35356L14.1463 10.1464C14.4613 10.4614 14.9999 10.2383 14.9999 9.7929V4.20712C14.9999 3.76166 14.4613 3.53858 14.1463 3.85356Z" />
                                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7 8C7.55228 8 8 8.44772 8 9V14C8 14.5523 7.55228 15 7 15H2.5C1.94772 15 1.5 14.5523 1.5 14C1.5 13.4477 1.94772 13 2.5 13H6V9C6 8.44772 6.44772 8 7 8Z" />
                                                                </svg>
                                                                <span class="progress-card__count-text">{{ $videosCnt }}</span>
                                                            </div>
                                                        @endif
                                                        @if($imagesCnt)
                                                            <div class="progress-card__count">
                                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9.945 2L10.918 4H13.64C14.394 4 15.001 4.624 15.001 5.4V12.6C15.001 13.376 14.394 14 13.64 14H2.361C1.607 14 1 13.376 1 12.6V5.4C1 4.624 1.607 4 2.361 4H5.083L6.056 2L9.945 2ZM8.001 5.4C6.075 5.4 4.501 7.019 4.501 9C4.501 10.981 6.075 12.6 8.001 12.6C9.927 12.6 11.501 10.981 11.501 9C11.501 7.019 9.927 5.4 8.001 5.4ZM7.996 6.6C9.289 6.6 10.324 7.667 10.324 9C10.324 10.333 9.289 11.4 7.996 11.4C6.703 11.4 5.668 10.333 5.668 9C5.668 7.667 6.703 6.6 7.996 6.6Z" />
                                                                </svg>
                                                                <span class="progress-card__count-text">{{ $imagesCnt }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
