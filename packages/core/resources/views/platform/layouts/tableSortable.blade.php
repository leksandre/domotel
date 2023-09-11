@empty(!$title)
    <fieldset>
        <div class="col p-0 px-3"><legend class="text-black">{{ $title }}</legend></div>
    </fieldset>
@endempty
<div class="bg-white rounded shadow-sm mb-3" data-controller="table" data-table-slug="{{$slug}}">
    <div class="table-responsive">
        <table class="table @if($striped) table-striped @endif  @if($bordered) table-bordered @endif @if($hoverable) table-hover @endif">
            <thead>
            <tr>
                @foreach($columns as $column)
                    {!! $column->buildTh() !!}
                @endforeach
            </tr>
            </thead>
            <tbody data-controller="sortable" data-sortable-handle-value=".handle" data-sortable-url="{{ $repository['sortableUrl'] ?? '' }}">
                @foreach($rows as $source)
                    <tr data-sort="{{ $source->getKey() }}">
                        @foreach($columns as $column)
                            {!! $column->buildTd($source) !!}
                        @endforeach
                    </tr>
                @endforeach
                @if(!empty($total) && $total->isNotEmpty())
                    <tr>
                        @foreach($total as $column)
                            {!! $column->buildTd($repository) !!}
                        @endforeach
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if(($rows instanceof \Illuminate\Contracts\Pagination\Paginator || $rows instanceof \Illuminate\Contracts\Pagination\CursorPaginator) && $rows->isEmpty())
        <div class="text-center py-5 w-100">
            <h3 class="fw-light">
                @isset($iconNotFound)
                    <x-orchid-icon :path="$iconNotFound" class="block m-b"/>
                @endisset
                {!!  $textNotFound !!}
            </h3>
            {!! $subNotFound !!}
        </div>
    @endif

    <footer class="pb-3 w-100 v-md-center px-4 d-flex flex-wrap">
        <div class="col-auto me-auto">
            @if(isset($columns) && \Orchid\Screen\TD::isShowVisibleColumns($columns))
                <div class="btn-group dropup d-inline-block">
                    <button type="button"
                            class="btn btn-sm btn-link dropdown-toggle p-0 m-0"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            data-bs-boundary="viewport"
                            aria-expanded="false">
                        {{ __('Configure columns') }}
                    </button>
                    <div class="dropdown-menu dropdown-column-menu dropdown-scrollable">
                        @foreach($columns as $column)
                            {!! $column->buildItemMenu() !!}
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </footer>
</div>


