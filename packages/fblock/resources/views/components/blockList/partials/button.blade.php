@php
    $button_text  = $button_text ?? trans('kelnik-fblock::front.components.blockList.button.text');
@endphp
<div class="flat-block-card__buttons">
    <button type="button" class="button {{ $button_class ?? 'j-popup-callback' }}" data-callback="true" data-href="{{ $formSlug }}" aria-label="{{ $button_text }}">
        <span>{{ $button_text }}</span>
    </button>
</div>
