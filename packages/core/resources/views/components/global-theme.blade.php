@if(!empty($colors) && $colors->isNotEmpty())
    @push('styles')<style>:root{@foreach($colors as $v){!! $v->getCssName(); !!}:{!! $v->getCssValue(); !!};{!! $v->getCssName(); !!}-rgb:{!! $v->getValueRGB(); !!};@endforeach}</style>@endpush
@endif
@if(empty($rounding))
    @push('styles')<style>:root{--is-br:0}</style>@endpush
@endif
@if(!empty($fonts) && $fonts->isNotEmpty())
    @php
      $preload = '';
    @endphp
    @push('styles')
    <style>
        @foreach($fonts as $fontWeight => $font)
            @@font-face {
                font-family: "MultiKelnik";
                src: url({{ $font->getUrl() }}) format("{{ $font->getExtension() }}");
                font-display: swap;
                font-weight: @if($fontWeight === 'bold') 700 @else 400 @endif;
            }
            @php
                if ($font->getExtension() === 'woff2') {
                    $preload .= '<link rel="preload" href="' . $font->getUrl() . '" as="font" type="font/woff2">' . "\n";
                }
            @endphp
        @endforeach
        html {font-family: "MultiKelnik", "ptRootUi", sans-serif;}
    </style>
    @endpush
    {!! $preload !!}
@endif
