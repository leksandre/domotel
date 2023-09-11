<tr>
    @if(!empty($sortable))
        <th width="32"><x-orchid-icon path="kelnik.sort" class="handle" height="1.5em" width="1.5em" /></th>
    @endif
    @foreach($columns as $column)
        <th class="p-1 align-middle">
            {!!
               $fields[$column]
                    ->value($row[$column] ?? '')
                    ->prefix($name)
                    ->id("$idPrefix-$key-$column")
                    ->name($keyValue ? $column : "[$key][$column]")
            !!}
        </th>
        @if ($loop->last && $removableRows)
            <th class="no-border text-center align-middle">
                <a href="#" data-action="matrix#deleteRow" class="small text-muted" title="Remove row">
                    <x-orchid-icon path="bs.trash3"/>
                </a>
            </th>
        @endif
    @endforeach
</tr>
