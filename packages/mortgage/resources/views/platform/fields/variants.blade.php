@component($typeForm, get_defined_vars())
    <table class="matrix table table-bordered border-right-0"
           data-controller="matrix kelnik-mortgage-variants"
           data-matrix-index="{{ $index }}"
           data-matrix-rows="{{ $maxRows }}"
           data-matrix-key-value="{{ var_export($keyValue) }}" {!! $attributes !!}>
        <thead>
        <tr>
            @if($sortable)
                <th></th>
            @endif
            @foreach($columns as $key => $column)
                <th scope="col" class="text-capitalize">
                    {{ is_int($key) ? $column : $key }}
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody @if($sortable) data-controller="sortable" data-sortable-handle-value=".handle" @endif>

        @foreach($value as $key => $row)
            @include('kelnik-mortgage::platform.partials.fields.variantRow', ['row' => $row, 'key' => $key])
        @endforeach
        <tr class="add-row">
            <th colspan="{{ count($columns) + ($sortable ? 1 : 0) }}" class="text-center p-0">
                <a href="#" data-action="matrix#addRow" class="btn btn-block small text-muted">
                    <x-orchid-icon path="plus-alt"/>
                    <span>{{ __('Add row') }}</span>
                </a>
            </th>
        </tr>
        <template class="matrix-template">
            @include('kelnik-mortgage::platform.partials.fields.variantRow', ['row' => [], 'key' => '{index}'])
        </template>
        </tbody>
    </table>
@endcomponent
