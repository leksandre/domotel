<section class="section section_theme_grey j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common"><h2 class="j-animation__header">{{ $title }}</h2></div>
            <div class="grid__news">
                <div class="main-news">
                    <div class="slider j-slider-news j-animation__slider">
                        <div class="slider__wrap j-slides">
                            @foreach($list as $el)
                                <a href="{{ $el->url }}" class="slider-news j-animation__row-item">
                                    @if($el->previewImage?->exists)
                                        <div class="slider-news__images">
                                            @if($el->previewImagePicture)
                                                {!! $el->previewImagePicture !!}
                                            @else
                                                <img src="{{ $el->previewImage->url }}" alt="{{ $el->previewImage->alt }}">
                                            @endif
                                        </div>
                                    @endif
                                    <div class="slider-news__content">
                                        <div class="slider-news__title">{{ $el->title }}</div>
                                        <div class="slider-news__data">{{ $el->publishDateFormatted }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
