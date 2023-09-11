@push('head')
    <link rel="shortcut icon" type="image/x-icon" href="/vendor/kelnik-core/favicons/kelnik_32.ico">
    <link rel="apple-touch-icon-precomposed" sizes="32x32" href="/vendor/kelnik-core/favicons/kelnik_32.png">
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="/vendor/kelnik-core/favicons/kelnik_57.png">
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="/vendor/kelnik-core/favicons/kelnik_76.png">
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="/vendor/kelnik-core/favicons/kelnik_120.png">
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="/vendor/kelnik-core/favicons/kelnik_152.png">
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="/vendor/kelnik-core/favicons/kelnik_180.png">
    <link rel="icon" sizes="192x192" href="/vendor/kelnik-core/favicons/kelnik_192.png">
    <meta name="robots" content="noindex"/>
    <meta name="google" content="notranslate">
    <meta name="theme-color" content="#21252a">
@endpush

<div class="h2 d-flex align-items-center">
    <p class="my-0 {{ auth()->check() ? 'd-none d-xl-block' : '' }}">
        <x-kelnik-core-complex-name />
    </p>
</div>
