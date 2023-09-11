<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="format-detection" content="telephone=no">
        {!! \Artesaos\SEOTools\Facades\SEOMeta::generate() !!}
        <x-kelnik-core-meta />
        <style>
            @php
                $cssPath = public_path('css/pdf/pdf.css');
                if (file_exists($cssPath)) {
                    include($cssPath);
                }
            @endphp
        </style>
        <style type="text/css" media="print">
            @page {
                size: a4;
                margin: 0;
            }
            .main {
                padding-top: 0;
                margin-top: 0;
                margin-bottom: 0;
            }
        </style>
        <x-kelnik-core-global-theme />
        @stack('styles')
    </head>
    <body>
        @yield('body')
    </body>
</html>
