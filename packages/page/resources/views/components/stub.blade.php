@if(isset($theme))
<x-kelnik-core-component-theme selector=".plug" :theme="$theme"/>
@endif
<div class="plug">
    @isset($background)
        <div class="plug__picture">
            @if($background instanceof \Orchid\Attachment\Models\Attachment)
                <img src="{{ $background->url }}" alt="{{ $background->alt }}">
            @else
                {!! $background !!}
            @endif
        </div>
    @endisset
    <div class="plug__container">
        @if(!empty($logo) && $logo instanceof \Orchid\Attachment\Models\Attachment)
            <div class="plug__logo">
                <img src="{{ $logo->url }}" alt="{{ $logo->alt }}">
            </div>
        @endif
        <div class="plug__content">
            @if (!empty($content['title']))
                <h1>{{ $content['title'] }}</h1>
            @endif
            @if (!empty($content['text']))
                <div class="plug__announcement">
                    {!! $content['text'] !!}
                </div>
            @endif
            <footer class="plug__footer">
                @if(!empty($content['phone']))<a href="tel:{{ $content['phoneLink'] }}">{{ $content['phone'] ?? '' }}</a>@endif
                @if(!empty($content['email']))<a href="mailto:{{ $content['email'] ?? '' }}">{{ $content['email'] ?? '' }}</a>@endif
            </footer>
        </div>
    </div>
</div>
