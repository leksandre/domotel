<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        {!! \Artesaos\SEOTools\Facades\SEOMeta::generate() !!}
        {!! \Artesaos\SEOTools\Facades\OpenGraph::generate() !!}
        <x-kelnik-core-meta />
        <x-kelnik-core-favicon />

        <link rel="stylesheet" href="{{ @mix('css/common/styles.css') }}">
        <x-kelnik-core-global-theme />
        @stack('styles')
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-KLDKSDN');</script>
        <x-kelnik-core-map-settings />
        <x-kelnik-core-js-codes />
    </head>
    <body {!! $bodyAttributes ?? '' !!}>
        @yield('body')
        @stack('footer')
        <x-kelnik-core-cookie-notice />
        <script async src="{{ @mix('/js/common/app.js') }}"></script>
        @stack('scripts')
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KLDKSDN" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    </body>
</html>
