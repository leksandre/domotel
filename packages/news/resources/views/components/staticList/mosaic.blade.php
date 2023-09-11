<section class="section section_theme_accent j-animation__section @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <h2 class="j-animation__header">{{ $title }}</h2>
                <div class="promotions j-animation__row" data-items="320:1,670:2,1280:3">
                    @foreach($list as $el)
                        <a href="{{ $el->url }}" class="promotion j-animation__row-item">
                            @if($el->previewImage?->exists)
                                <div class="promotion__image">
                                    @if($el->previewImagePicture)
                                        {!! $el->previewImagePicture !!}
                                    @else
                                        <img src="{{ $el->previewImage->url }}" alt="{{ $el->previewImage->alt }}">
                                    @endif
                                </div>
                            @endif
                            <div class="promotion__content">
                                <div class="promotion__title">{{ $el->title }}</div>
                                <div class="promotion__labels">
                                    @if($el->publishDateFinishFormatted)
                                        <span class="promotion__label">@lang('kelnik-news::front.element.activeToListed', ['dateTo' => $el->publishDateFinishFormatted])</span>
                                    @else
                                        <span class="promotion__label">@lang('kelnik-news::front.element.permanentAction')</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
