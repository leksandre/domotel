<div id="visual"
     data-url="{!! $url !!}"
     data-base-url="{!! $baseUrl !!}"
     data-plural="{!! $plural ?? '' !!}"
     @if(!empty($step['id']))
        data-first-step="{{ $step['name'] }}"
        data-first-step-id="{{ $step['id'] }}"
     @endif
     data-property-type="flat"></div>
@if(!empty($callbackForm))
    <x-kelnik-form :params="$callbackForm" />
@endif
@if(!empty($assets['css']))
    @foreach($assets['css'] as $filePath)
        @push('styles')
            <link href="{!! $filePath !!}" rel="preloaded" as="style">
            <link href="{!! $filePath !!}" rel="stylesheet">
        @endpush
    @endforeach
@endif

@if(!empty($assets['js']))
    @foreach($assets['js'] as $filePath)
        @push('scripts')
            <script src="{!! $filePath !!}"></script>
        @endpush
        @push('styles')
            <link href="{!! $filePath !!}" rel="preloaded" as="script">
        @endpush
    @endforeach
@endif
