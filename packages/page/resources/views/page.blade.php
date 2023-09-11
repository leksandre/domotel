@extends('kelnik-page::app')
@section('body')
    {!! $header ?? '' !!}
    <main class="grid__main j-theme-main {{ $cssClasses ?? '' }}" data-theme="default">{!! $content ?? '' !!}</main>
    {!! $footer ?? '' !!}
@endsection
@if(!empty($stacks))
    @foreach($stacks as $stackName => $html)
        @push($stackName) {!! $html !!} @endpush
    @endforeach
@endif
