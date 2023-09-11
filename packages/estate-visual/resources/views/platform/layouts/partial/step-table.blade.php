<div class="kelnik-estate-visual-table bg-white rounded shadow-sm mb-3">
    <div class="table-responsive">
        <table class="table mb-0">
            <tbody>
            @foreach($rows as $el)
                <tr class="@if(!$el->exists) table-danger @endif @if($loop->last) no-border @endif">
                    @foreach($columns as $column)
                        {!! $column->buildTd($el) !!}
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
