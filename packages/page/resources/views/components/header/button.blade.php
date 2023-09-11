@php
    $buttonText  = $buttonText ?? trans('kelnik-page::front.components.header.callbackButton.text');
@endphp
<button type="button" class="button header__callback-button j-popup-callback" data-callback="true" data-href="{{ $formSlug }}" aria-label="{{ $buttonText }}">
    <span>{{ $buttonText }}</span>
</button>
<button type="button" class="button-circle header__mobile-phone j-popup-callback" data-callback="true" data-href="{{ $formSlug }}" aria-label="{{ $buttonText }}">
    <svg width="14" height="14" viewBox="0 0 14 14">
        <path id="rrb6a" d="M11.748 0h.298c.996.402 1.693 1.91 1.892 2.916.498 3.318-2.09 6.234-4.58 8.145-2.09 1.709-6.272 4.524-8.661 1.81-.398-.202-.697-.704-.697-1.207.1-.804.796-1.408 1.394-1.81.398-.302 1.394-1.207 1.99-1.207.499 0 .897.604 1.196.905l.497.503c.1.1 1.295-.704 1.494-.804a7.53 7.53 0 0 0 1.394-1.106c.398-.403.796-.905 1.095-1.307.1-.202.896-1.408.896-1.509 0 0-.697-.704-.896-1.005-.399-.604-.697-1.106-.299-1.81.2-.302.398-.503.597-.805.399-.402.797-.804 1.295-1.106.298-.2.696-.502 1.095-.603z" />
    </svg>
</button>
