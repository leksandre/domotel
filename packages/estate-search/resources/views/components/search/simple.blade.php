<section class="section">
    <div class="grid">
        <div class="grid__row">
            <div class="grid__parametric">
                <div id="parametric"
                     data-url="{!! $url !!}"
                     data-base-url="{!! $baseUrl !!}"
                     data-title="{{ $title }}"
                     data-plural="{!! $plural !!}"></div>
                @foreach($assets['css'] as $filePath)
                    @push('styles')
                        <link href="{!! $filePath !!}" rel="preloaded" as="style">
                        <link href="{!! $filePath !!}" rel="stylesheet">
                    @endpush
                @endforeach

                @foreach($assets['js'] as $filePath)
                    @push('scripts')
                        <script src="{!! $filePath !!}"></script>
                    @endpush
                    @push('styles')
                        <link href="{!! $filePath !!}" rel="preloaded" as="script">
                    @endpush
                @endforeach
            </div>
        </div>
    </div>
</section>
@if(!empty($callbackForm))
    <x-kelnik-form :params="$callbackForm" />
@endif
