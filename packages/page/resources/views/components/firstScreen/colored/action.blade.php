<div class="first-screen__lead-container">
    <div class="first-screen__lead-content">
        <a href="{{ $buttonLink ?? $action->url }}" class="first-screen__lead {{ $cssClass ?? '' }}">
            @if(!empty($iconBody))
                <div class="first-screen__lead-icon">{!! $iconBody !!}</div>
            @elseif(!empty($iconPath))
                <div class="first-screen__lead-icon"><img src="{!! $iconPath !!}" alt="" /></div>
            @endif
            <span class="first-screen__lead-title">{{ $buttonText ?? $action->title }}</span>
            <div class="first-screen__lead-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.62688 4.29289C8.0174 3.90237 8.65057 3.90237 9.04109 4.29289L14.0411 9.29289C14.4316 9.68342 14.4316 10.3166 14.0411 10.7071L9.04109 15.7071C8.65057 16.0976 8.0174 16.0976 7.62688 15.7071C7.23635 15.3166 7.23635 14.6834 7.62688 14.2929L11.9198 10L7.62688 5.70711C7.23635 5.31658 7.23635 4.68342 7.62688 4.29289Z" />
                </svg>
            </div>
        </a>
    </div>
</div>
