@php
    $hasSlider = $el->imageSlider && $el->imageSlider->isNotEmpty();
    $hasImages = $el->relationLoaded('images') && $el->images->isNotEmpty();
    $usePop = !empty($el->planoplan_code) && ($showPop ?? false);
@endphp
@if($hasSlider || $hasImages || $usePop)
    <div class="slider slider_theme_flats-card-slider j-slider-flat-block">
        <div class="slider__wrap j-slides">
            @if($usePop)
                <div class="flat-block-card__gallery">
                    @php
                        $popParams = [
                            'uid' => $el->planoplan_code,
                            'el' => $pcId . '-fblock_' . $el->id . '-' . $suffix,
                            'primaryColor' => $colors['primary'] ?? '#95d0a1',
                            'secondaryColor' => '#F4F7F7',
                            'textColor' => $colors['text'] ?? '#00000',
                            'backgroundColor' => '#FFFFFF'
                        ];
                    @endphp
                    <div class="flat-block-card__widget j-slider__widget" id="{{ $popParams['el'] }}" data-planoplan="{!! base64_encode(json_encode($popParams)) !!}"></div>
                </div>
            @endif
            @if($hasSlider)
                @foreach($el->imageSlider as $slide)
                    <div class="flat-block-card__gallery">
                        {!! $slide['picture'] !!}
                        <div class="flat__controls">
                            <button class="flat__fullscreen-button j-popup-slider"
                                    data-gallery="true"
                                    data-slider="{{ $pcId }}-{{ $el->getKey() }}-{{ $suffix }}"
                                    data-src="{!! $slide['url'] !!}"
                                    data-alt="{{ $slide['alt'] ?? $el->title }}"
                                    data-caption=""
                                    aria-label="{{ __('kelnik-fblock::front.components.blockList.popupLabel') }}">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            @elseif($hasImages)
                @foreach($el->images as $slide)
                    <div class="flat-block-card__gallery">
                        <img loading="lazy" data-src="{!! $slide->url() !!}" alt="{{ $slide->alt ?? $el->title }}">
                        <div class="flat__controls">
                            <button class="flat__fullscreen-button j-popup"
                                    data-gallery="true"
                                    data-slider="{{ $pcId }}-{{ $el->getKey() }}-{{ $suffix }}"
                                    data-src="{!! $slide->url() !!}"
                                    data-alt="{{ $slide->alt ?? $el->title }}"
                                    data-caption=""
                                    aria-label="{{ __('kelnik-fblock::front.components.blockList.popupLabel') }}">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1 4C1.55228 4 2 4.44772 2 5V10H7C7.55228 10 8 10.4477 8 11C8 11.5523 7.55228 12 7 12H0V5C0 4.44772 0.447715 4 1 4Z" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4 1C4 0.447715 4.44772 0 5 0H12V7C12 7.55228 11.5523 8 11 8C10.4477 8 10 7.55228 10 7V2H5C4.44772 2 4 1.55228 4 1Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endif
