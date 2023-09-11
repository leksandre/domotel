@if(!empty($codes['head']))
    {!! implode('', $codes['head']) !!}
@endif
@if(!empty($codes['body']))
    @push('scripts')
    {!! implode('', $codes['body']) !!}
    @endpush
@endif
