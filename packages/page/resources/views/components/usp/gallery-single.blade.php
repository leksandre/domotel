@if(!empty($slider) && $slider->isNotEmpty())
    <div class="grid__visual j-animation__item">
        <div class="gallery-mini">
            <div class="slider j-slider-mini">
                <div class="slider__wrap j-slides">
                    @foreach($slider as $slide)
                        <div class="slider-mini" data-caption="{{ $slide['description'] }}">
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
