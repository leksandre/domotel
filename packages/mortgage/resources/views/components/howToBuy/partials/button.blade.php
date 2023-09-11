@php
    $button_text  = $button_text ?? trans('kelnik-mortgage::front.components.howToBuy.button.text');
@endphp
<button type="button" class="button j-popup-callback" data-callback="true" data-href="{{ $formSlug }}" aria-label="{{ $button_text }}">
    <span>{{ $button_text }}</span>
</button>
