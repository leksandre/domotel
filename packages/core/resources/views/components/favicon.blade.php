@foreach([57, 60, 72, 76, 114, 120, 144, 152] as $width)
    @isset($sizes[$width])
    <link rel="apple-touch-icon-precomposed" sizes="{{$width}}x{{$width}}" href="{!! $sizes[$width] !!}">
@endisset
@endforeach
@isset($sizes[180])
        <link rel="apple-touch-icon" sizes="180x180" href="{!! $sizes[180] !!}">
@endisset
@foreach([192, 96, 48, 32, 16] as $width)
    @isset($sizes[$width])
    <link rel="icon" type="image/png" sizes="{{$width}}x{{$width}}" href="{!! $sizes[$width] !!}">
@endisset
@endforeach
@isset($sizes[144])
        <meta name="msapplication-TileImage" content="{!! $sizes[144] !!}">
@endisset
@foreach([70, 150, 310] as $width)
    @isset($sizes[$width])
    <meta name="msapplication-square{{$width}}x{{$width}}logo" content="{!! $sizes[$width] !!}">
@endisset
@endforeach
