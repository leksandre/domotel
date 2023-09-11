@component($typeForm, get_defined_vars())
    <table class="matrix table table-bordered border-right-0"
           data-controller="fields--matrix"
           data-fields--matrix-index="{{ $index }}"
           data-fields--matrix-rows="{{ $maxRows }}"
           data-fields--matrix-key-value="{{ var_export($keyValue) }}">
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
            @include('kelnik-core::platform.partials.fields.matrixStaticRow', ['row' => $row, 'key' => $key])
        @endforeach

        </tbody>
    </table>
@endcomponent
