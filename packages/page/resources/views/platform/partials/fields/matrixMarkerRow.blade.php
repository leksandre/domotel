<tr>
    @if(!empty($sortable))
        <th width="32"><x-orchid-icon path="kelnik.sort" class="handle" height="1.5em" width="1.5em" /></th>
    @endif
    @foreach($columns as $column)
        <th class="p-1 align-middle">
            @php
                $field = $fields[$column];
                $hiddenField = $field->get('data-hidden-field') ?? null;
                $hiddenFieldName = '';
                if ($hiddenField) {
                    $hiddenFieldName = "{$name}[$key][$hiddenField]";
                }
            @endphp
            {!!
                $field
                    ->value($row[$column] ?? '')
                    ->prefix($name)
                    ->id("$idPrefix-$key-$column")
                    ->name($keyValue ? $column : "[$key][$column]")
            !!}
            @if($hiddenField)
                <input type="hidden" name="{{ $hiddenFieldName }}" value="{{ $row[$hiddenField] ?? '' }}">
            @endif
        </th>
        @if ($loop->last)
            <th class="no-border text-center align-middle">
                <a href="#"
                   data-action="matrix#deleteRow"
                   class="small text-muted"
                   title="Remove row">
                    <x-orchid-icon path="trash"/>
                </a>
            </th>
        @endif
    @endforeach
</tr>
