<aside class="grid__factoid">
    @if(!empty($list)) <h5>{{ $title }}</h5> @endif
    <div class="factoid-cards">
        @foreach($list as $element)
            <a href="{{ $element->url }}" class="factoid-card">
                @if($element->previewImage?->exists)
                    <div class="factoid-card__image">
                        @if($element->previewImagePicture)
                            {!! $element->previewImagePicture !!}
                        @else
                            <img src="{{ $element->previewImage->url }}" alt="{{ $el->previewImage->alt }}">
                        @endif
                    </div>
                @endif
                <div class="factoid-card__content">
                    <p class="factoid-card__announcement">{{ $element->title }}</p>
                    @if($element->publishDateFinishFormatted)
                        <div class="factoid-card__footer">
                            <span class="factoid-card__comment">@lang('kelnik-news::front.element.activeToListed', ['dateTo' => $element->publishDateFinishFormatted])</span>
                        </div>
                    @else
                        <div class="factoid-card__footer">
                            <span class="factoid-card__comment">@lang('kelnik-news::front.element.permanentAction')</span>
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</aside>
