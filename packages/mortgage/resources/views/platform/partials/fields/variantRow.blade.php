<tr>
    @if(!empty($sortable))
        <th width="32"><x-orchid-icon path="kelnik.sort" class="handle" height="1.5em" width="1.5em" /></th>
    @endif
    @foreach($columns as $column)
        <th class="p-1 align-middle">
            @php
                $value = $row[$column] ?? '';
                $originField = $fields[$column]->get('data-origin');
                if ($originField) {
                    $value = $row[$originField] ?? '';
                }
            @endphp
            {!!
               $fields[$column]
                    ->value($value)
                    ->prefix($name)
                    ->id("$idPrefix-$key-$column")
                    ->name($keyValue ? $column : "[$key][$column]")
            !!}
        </th>
        @if ($loop->last)
            <th class="text-center align-middle">
                <a href="#" data-action="click->kelnik-mortgage-variants#openModal" class="toggle-modal" data-key="{{ $key }}"><x-orchid-icon path="bs.gear" /></a>
                <input type="hidden" data-key="{{ $key }}" data-field="title" name="{{ $prefix . $name }}[{{ $key }}][title]" value="{{ $row['title'] ?? '' }}">
                <input type="hidden" data-key="{{ $key }}" data-field="text" name="{{ $prefix . $name }}[{{ $key }}][text]" value="{{ $row['text'] ?? '' }}">
                <input type="hidden" data-key="{{ $key }}" data-field="active" name="{{ $prefix . $name }}[{{ $key }}][active]" value="{!! (int)($row['active'] ?? 0) !!}">
                <input type="hidden" data-key="{{ $key }}" data-field="showBanks" name="{{ $prefix . $name }}[{{ $key }}][showBanks]" value="{{ $row['showBanks'] ?? \Kelnik\Mortgage\View\Components\HowToBuy\HowToBuy::BANKS_VIEW_OFF }}">
            </th>
            <th class="no-border text-center align-middle">
                <a href="#" data-action="matrix#deleteRow" class="small text-muted" title="Remove row">
                    <x-orchid-icon path="bs.trash3"/>
                </a>
            </th>
        @endif
    @endforeach
</tr>
