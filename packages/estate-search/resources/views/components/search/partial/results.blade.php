<div class="parametric-result__flats j-parametric-result__content">
    @foreach($items as $el)
        <a href="{{ $el->url }}" class="flat-ticket">
            <div class="flat-ticket__content">
                <div class="flat-ticket__img">
                    @if($el->relationLoaded('imageList') || $el->relationLoaded('imagePlan'))
                        <div class="flat-ticket__img-wrapper">
                            <img src="{!! $el->relationLoaded('imageList') ? $el->imageList->url() : $el->imagePlan->url() !!}" alt="{{ $el->typeShortTitle }}">
                        </div>
                    @else
                        <div class="flat-ticket__img-wrapper">
                            <img src="{!! $el->imagePlanDefault !!}" alt="{{ $el->typeShortTitle }}">
                        </div>
                    @endif
                    @if($el->status?->relationLoaded('icon') || $el->status?->icon->exists)
                        <span class="button-round button-round_theme_green-icon flat-ticket__lock" title="{{ $el->status->additional_text }}">
                            <img src="{!! $el->status->icon->url() !!}" />
                        </span>
                    @endif
                </div>
                <div class="flat-ticket__info">
                    <div class="flat-ticket__info-row">
                        <div class="flat-ticket__info-block">
                            <div class="flat-ticket__area">{{ number_format($el->area_total, 1, ',', ' ') }}<span class="flat-ticket__area-symbol">м²</span></div>
                        </div>
                        <div class="flat-ticket__info-block">
                            <div class="flat-ticket__price">{{ number_format($el->price_total, 0, ',', ' ') }} ₽</div>
                        </div>
                    </div>
                    <div class="flat-ticket__info-row">
                        <div class="flat-ticket__parameters">
                            <div class="flat-ticket__info-text">{{ $el->typeShortTitle }}</div>
                            <div class="flat-ticket__info-text">Этаж {{ $el->floor->title }}@if($el->floor_max) из {{ $el->floor_max }}@endif</div>
                        </div>
                        @if($el->floor->relationLoaded('building'))
                            <div class="flat-ticket__parameters">
                                <div class="flat-ticket__info-text">{{ $el->floor->building->title }}</div>
                            </div>
                        @endisset
                    </div>
                </div>
                <div class="flat-ticket__additional">
                    @if($el->relationLoaded('features') && $el->features->isNotEmpty())
                        <div class="flat-ticket__options-wrapper j-flat-options-container">
                            <button type="button"
                                    class="button-round button-round_theme_green-icon flat-ticket__options-more j-parametric-card__show-more"
                                    aria-label="Показать дополнительные параметры">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg" class="button-round__icon">
                                    <path d="M12 20C13.1046 20 14 19.1046 14 18C14 16.8954 13.1046 16 12 16C10.8954 16 10 16.8954 10 18C10 19.1046 10.8954 20 12 20Z"/>
                                    <path d="M12 8C13.1046 8 14 7.10457 14 6C14 4.89543 13.1046 4 12 4C10.8954 4 10 4.89543 10 6C10 7.10457 10.8954 8 12 8Z"/>
                                    <path d="M12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14Z"/>
                                </svg>
                            </button>
                            <ul class="flat-ticket__options j-flat-options">
                                @foreach($el->features as $feature)
                                    <li class="flat-ticket__option">
                                        @if($feature->relationLoaded('icon'))
                                            <img src="{!! $feature->icon->url() !!}" class="flat-ticket__option-img" alt="{{ $feature->title }}">
                                        @endif
                                        <div class="flat-ticket__option-text">{{ $feature->title }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="flat-ticket__buttons">
                        <?php /*<button type="button" class="button-round button-round_theme_green-icon flat-ticket__favorite j-favorite" aria-label="Добавить в избранное" data-uuid="{{ $el->external_id }}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="button-round__icon">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.17157 6.20101C6.73367 4.59966 9.26633 4.59966 10.8284 6.20101L12 7.40202L13.1716 6.20101C14.7337 4.59966 17.2663 4.59966 18.8284 6.20101C20.3905 7.80236 20.3905 10.3987 18.8284 12L12.3579 18.6331C12.1617 18.8342 11.8383 18.8342 11.6421 18.6331L5.17157 12C3.60948 10.3987 3.60948 7.80236 5.17157 6.20101Z"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.2915 6.72472C9.02378 5.4251 6.97621 5.42509 5.70844 6.72472C4.43051 8.03476 4.43053 10.1663 5.70843 11.4763L12 17.9259L18.2915 11.4763C19.5694 10.1663 19.5695 8.03476 18.2915 6.72472C17.0238 5.42509 14.9762 5.42509 13.7085 6.72472L12 8.47608L10.2915 6.72472ZM11.3653 5.6773C9.50886 3.77423 6.49112 3.77424 4.6347 5.6773C2.78844 7.56996 2.78843 10.6311 4.6347 12.5237C4.6347 12.5237 4.6347 12.5237 4.6347 12.5237L11.1052 19.1568C11.5957 19.6596 12.4042 19.6596 12.8947 19.1569L19.3653 12.5237C21.2115 10.6311 21.2115 7.56996 19.3653 5.6773C17.5088 3.77423 14.4912 3.77423 12.6347 5.6773L12 6.32797L11.3653 5.6773C11.3653 5.6773 11.3653 5.67729 11.3653 5.6773Z"/>
                            </svg>
                        </button>*/?>
                        <span class="button-round button-round_theme_green-icon flat-ticket__link">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="button-round__icon">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.2197 6.21967C12.9268 6.51256 12.9268 6.98744 13.2197 7.28033L16.9393 11H4.75C4.3358 11 4 11.3358 4 11.75C4 12.1642 4.3358 12.5 4.75 12.5H16.9393L13.2197 16.2197C12.9268 16.5126 12.9268 16.9874 13.2197 17.2803C13.5126 17.5732 13.9874 17.5732 14.2803 17.2803L19.2803 12.2803C19.3522 12.2084 19.4065 12.1255 19.4431 12.0371C19.4798 11.9487 19.5 11.8517 19.5 11.75C19.5 11.6483 19.4798 11.5513 19.4431 11.4629C19.4065 11.3744 19.3522 11.2916 19.2803 11.2197L14.2803 6.21967C13.9874 5.92678 13.5126 5.92678 13.2197 6.21967Z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>
<div class="parametric-result__more">
    <button type="button" class="button button_theme_small j-parametric-result__more @if(!$more['enable']) is-hidden @endif" aria-label="{{ $more['text'] }}" data-shown="{{ $items->count() }}">
        <span class="button__inner">
            <span class="button__text j-parametric-result__more-text">{{ $more['text'] }}</span>
        </span>
    </button>
</div>
