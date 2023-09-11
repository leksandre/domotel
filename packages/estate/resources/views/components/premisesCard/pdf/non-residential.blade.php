@extends('kelnik-pdf::app')
@section('body')
    @php
        $title = $element->typeShortTitle ?? $element->title;
        $priceVisible = $element->price_is_visible && $element->price_total;
    @endphp
    <main class="main">
        <div class="pdf">
            <div class="pdf__container">
                @include('kelnik-estate::components.premisesCard.pdf.partial.header')
                @include('kelnik-estate::components.premisesCard.pdf.partial.main-non-residential')
                @include('kelnik-estate::components.premisesCard.pdf.partial.footer')
            </div>
            <div class="pdf__container">
                @include('kelnik-estate::components.premisesCard.pdf.partial.header')
                @includeWhen(!empty($about), 'kelnik-estate::components.premisesCard.pdf.partial.about')
                @includeWhen(!empty($contacts), 'kelnik-estate::components.premisesCard.pdf.partial.contacts')
                @include('kelnik-estate::components.premisesCard.pdf.partial.footer')
            </div>
        </div>
    </main>
@endsection
