<section class="page-header page-header_theme_promo">
    @if($element->bodyImagePicture)
        <div class="page-header__background">{!! $element->bodyImagePicture !!}</div>
    @endif
    <div class="page-header__wrap">
        <div class="page-header__content">
            <div class="page-header__breadcrumbs">
                <ul class="breadcrumbs">
                    <li class="breadcrumb__item breadcrumb__item_show_mobile">
                        <button class="breadcrumb__back-btn" onclick="history.back();">
                            <span class="breadcrumb__back-btn-icon">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.8">
                                        <path opacity="0.8" fill-rule="evenodd" clip-rule="evenodd" d="M7.35355 4.64645C7.54882 4.84171 7.54882 5.15829 7.35355 5.35355L5.20711 7.5H12.5C12.7761 7.5 13 7.72386 13 8C13 8.27614 12.7761 8.5 12.5 8.5H5.20711L7.35355 10.6464C7.54882 10.8417 7.54882 11.1583 7.35355 11.3536C7.15829 11.5488 6.84171 11.5488 6.64645 11.3536L3.64665 8.35375C3.64658 8.35369 3.64651 8.35362 3.64645 8.35355C3.64574 8.35285 3.64503 8.35214 3.64433 8.35143C3.59744 8.30398 3.56198 8.24949 3.53794 8.19139C3.51349 8.13244 3.5 8.06779 3.5 8C3.5 7.86301 3.55509 7.73888 3.64433 7.64857C3.64503 7.64786 3.64574 7.64715 3.64645 7.64645C3.64651 7.64638 3.64658 7.64631 3.64665 7.64625L6.64645 4.64645C6.84171 4.45118 7.15829 4.45118 7.35355 4.64645Z" fill="white" />
                                    </g>
                                </svg>
                            </span>
                            <span class="breadcrumb__back-btn-text">{{ trans('kelnik-news::front.elementCard.back') }}</span>
                        </button>
                    </li>
                    @if(!empty($breadcrumbs))
                        @foreach($breadcrumbs as $el)
                            <li class="breadcrumb__item">
                                @if($loop->last)
                                    <span class="breadcrumb">{{ $el[0] }}</span>
                                @else
                                    <a class="breadcrumb" href="{{ $el[1] }}">{{ $el[0] }}</a>
                                    <span class="breadcrumb__delimiter">/</span>
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <div class="page-header__title"><h1>{{ $element->title }}</h1></div>
            @if($element->publishDateFinishFormatted)
                <div class="page-header__footnote">@lang('kelnik-news::front.element.activeToListed', ['dateTo' => $element->publishDateFinishFormatted])</div>
            @else
                <div class="page-header__footnote">@lang('kelnik-news::front.element.permanentAction')</div>
            @endif
        </div>
    </div>
</section>

<div class="section section_theme_promo">
    <div class="grid grid_side_laptop">
        <div class="grid__common">
            <div class="grid__wrapper">
                <div class="grid__row">
                    <article class="grid__content">
                        <div class="grid__content-section">{!! $element->body !!}</div>
                        @if($element->button->getText())
                            <div class="grid__content-section">
                                <a href="{!! $element->button->getLink() !!}" target="{{ $element->button->getTarget() }}" class="button">{{ $element->button->getText() }}</a>
                            </div>
                        @endif
                        @if($element->imageSlider && $element->imageSlider->isNotEmpty())
                            <div class="grid__content-section">
                                <div class="gallery-mini">
                                    <div class="slider j-slider-mini">
                                        <div class="slider__wrap j-slides">
                                            @foreach($element->imageSlider as $slide)
                                                <div class="slider-mini">
                                                    <div class="slider-mini__inner">
                                                        @if(!$slide['picture'])
                                                            <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}">
                                                        @else
                                                            {!! $slide['picture'] !!}
                                                        @endif
                                                        <button class="slider__fullscreen-button j-popup-slider"
                                                                data-gallery="true"
                                                                data-src="{{ $slide['url'] }}"
                                                                data-alt="{{ $slide['description'] }}"
                                                                data-caption="{{ $slide['description'] }}"
                                                                data-slider="{{ $slide['code'] }}"
                                                                aria-label="{{ __('kelnik-page::front.popupLabel') }}">
                                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </article>
                    <x-kelnik-news-other-list :params="$listParams ?? null" />
                </div>
            </div>
        </div>
    </div>
</div>
