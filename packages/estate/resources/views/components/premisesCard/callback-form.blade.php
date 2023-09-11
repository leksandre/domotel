@php
    $buttonText = $buttonText ?? trans('kelnik-estate::front.components.premisesCard.callbackButton.text');
@endphp
<button type="button" class="button j-popup-callback" data-callback="true" data-href="{{ $formSlug }}" aria-label="{{ $buttonText }}"><span>{{ $buttonText }}</span></button>
