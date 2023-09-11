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
                    ->id("$column-$key-$column")
                    ->name($keyValue ? $column : "[$key][$column]")
            !!}
        </th>
    @endforeach
</tr>
