<div class="page-error j-first-screen-bg page-error_theme_dark">
    <div class="page-error__picture">
        @if (is_object($background))
            <img src="{!! $background->url() !!}" alt="{{ $background->alt ?? $title }}">
        @elseif(is_string($background) && $background)
            {!! $background !!}
        @endif
    </div>
    <div class="grid">
        <div class="grid__row">
            <div class="grid__common">
                <div class="page-error__content">
                    <div class="page-error__number">{{ trans('kelnik-page::front.components.errorInfo.number', ['value' => $code]) }}</div>
                    <h1 class="page-error__title">{{ $title }}</h1>
                    <div class="page-error__description">{!! $text !!}</div>
                    @if($buttons)
                        <div class="page-error__buttons">
                            @isset($buttons[0])
                                <a href="{!! $buttons[0]['url'] !!}" class="button page-error__button button_theme_color">{{ $buttons[0]['title'] }}</a>
                            @endif
                            @isset($buttons[1])
                                <a href="{!! $buttons[1]['url'] !!}" class="button page-error__button button_theme_white">{{ $buttons[1]['title'] }}</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
