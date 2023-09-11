<div class="cookies-notice j-cookies-notice" data-timer="{{ $expired ?? \Kelnik\Core\Platform\Services\Contracts\SettingsPlatformService::EXPIRED_DEFAULT }}">
    <div class="cookies-notice__text">
        {!! $text ?? '' !!}
        @if(empty($link))
            <button class="j-popup" data-no-hash="true" data-href="cookies" data-modify="content">{{ $linkText ?? trans('kelnik-core::front.cookieNotice.linkText') }}</button>
        @else
            <a href="{!! $link !!}">{{ $linkText ?? trans('kelnik-core::front.cookieNotice.linkText') }}</a>
        @endif
    </div>
    <div class="cookies-notice__button">
        <button type="button" class="button button_theme_color j-cookies-notice__button" aria-label="{{ $buttonText ?? trans('kelnik-core::front.cookieNotice.buttonText') }}">
            <span>{{ $buttonText ?? trans('kelnik-core::front.cookieNotice.buttonText') }}</span>
        </button>
    </div>
</div>
@if(empty($link))
<template id="cookies">{!! $popupText ?? '' !!}</template>
@endif
