@if(!empty($colors) && $colors->isNotEmpty())
    @push('styles')
        <style>{!! $selector !!}{@foreach($colors as $v) {!! $v->getCssName() !!}:{!! $v->getCssValue(); !!};@endforeach}</style>
    @endpush
@endif
