@php
    $styles = [
        \Kelnik\Page\View\Components\Header\Enums\Style::Adaptive->value => 'j-header',
        \Kelnik\Page\View\Components\Header\Enums\Style::AdaptiveTrans->value => 'header_theme_transparent j-header',
        \Kelnik\Page\View\Components\Header\Enums\Style::White->value => 'header_theme_white',
        \Kelnik\Page\View\Components\Header\Enums\Style::Transparent->value => 'header_theme_transparent',
        \Kelnik\Page\View\Components\Header\Enums\Style::Fixed->value => 'is-fixed',
        \Kelnik\Page\View\Components\Header\Enums\Style::FixedTransparent->value => 'is-fixed header_theme_transparent'
    ];

    $defaultStyle = \Kelnik\Page\View\Components\Header\Enums\Style::AdaptiveTrans->value;
    $cssClasses = $styles[$style ?? $defaultStyle] ?? $defaultStyle;
@endphp
@if(isset($logoHeight))
    @push('styles')<style>:root{ --header-logo-height: {{ $logoHeight }}px; }</style>@endpush
@endif
<header class="header {{ $cssClasses }}">
    <div class="header__container">
        <div class="header__inner j-header__inner">
            <a class="header__logo j-header__drive" href="{{ $homeLink ?? '/' }}">
                @if(!empty($logoLight['path']))
                    <img class="header__logo-light" src="{{ $logoLight['path'] }}" width="{{ $logoLight['width'] }}" height="{{ $logoLight['height'] }}" alt="{{ $complexName ?? '' }}">
                @endif
                @if(!empty($logoDark['path']))
                    <img class="header__logo-dark" src="{{ $logoDark['path'] }}" width="{{ $logoDark['width'] }}" height="{{ $logoDark['height'] }}" alt="{{ $complexName ?? '' }}">
                @endif
            </a>
            @if(!empty($menuDesktop))
                <x-kelnik-menu :params="$menuDesktop" />
            @endif

            <div class="header__lead j-header__drive">
                <div class="header__phone">
                    <a href="tel:{{ $phoneLink }}" class="phone"><span class="phone__number">{{ $phone }}</span></a>
                </div>
                @if(!empty($callbackForm))
                    <x-kelnik-form :params="$callbackForm" />
                @endif

                @if(!empty($menuUniversal))
                    <button class="header__burger j-burger" aria-label="{{ trans('kelnik-page::front.components.header.openMenu') }}">
                        <span class="header__burger-text">{{ trans('kelnik-page::front.components.header.menu') }}</span>
                        <span class="header__burger-line"></span>
                    </button>
                @else
                    <button class="header__burger j-burger" aria-label="{{ trans('kelnik-page::front.components.header.openMenu') }}"><span class="header__burger-line"></span></button>
                @endif
            </div>

            @if(!empty($menuUniversal))
                <x-kelnik-menu :params="$menuUniversal" />
            @elseif(!empty($menuMobile))
                <x-kelnik-menu :params="$menuMobile" />
            @endif
        </div>
    </div>
</header>
