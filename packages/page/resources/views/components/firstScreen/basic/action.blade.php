<div class="first-screen__lead-container">
    <div class="first-screen__lead">
        @if($action->publishDateFinishFormatted)
            <span class="first-screen__lead-label">@lang('kelnik-news::front.element.activeTo', ['dateTo' => $action->publishDateFinishFormatted])</span>
        @else
            <span class="first-screen__lead-label">@lang('kelnik-news::front.element.permanentAction')</span>
        @endif
        <a href="{{ $action->url }}" class="first-screen__lead-title">{{ $action->title }}</a>
        @if(!empty($buttonText) && !empty($buttonLink))
            <a href="{!! $buttonLink !!}" class="first-screen__lead-link">
                <span>{{ $buttonText }}</span>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M8 3.5C8.27614 3.5 8.5 3.72386 8.5 4V11.2929L10.6464 9.14645C10.8417 8.95118 11.1583 8.95118 11.3536 9.14645C11.5488 9.34171 11.5488 9.65829 11.3536 9.85355L8 13.2071L4.64645 9.85355C4.45118 9.65829 4.45118 9.34171 4.64645 9.14645C4.84171 8.95118 5.15829 8.95118 5.35355 9.14645L7.5 11.2929V4C7.5 3.72386 7.72386 3.5 8 3.5Z"/>
                </svg>
            </a>
        @endif
    </div>
</div>
