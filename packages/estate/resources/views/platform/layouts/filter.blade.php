@if(!empty($filters) && $filters->isNotEmpty())
    <div class="g-0 bg-white rounded mb-3">
        <div class="row align-items-center p-3" data-controller="filter">
            @foreach($filters->where('display', true) as $filter)
                <div class="col-sm-auto mb-3 align-self-start">
                    {!! $filter->render() !!}
                </div>
            @endforeach
            <div class="col-sm-auto ms-auto text-end">
                <div class="btn-group" role="group">
                    <button data-action="filter#clear" class="btn btn-default">
                        <x-orchid-icon class="me-1" path="refresh"/> {{ trans('kelnik-estate::admin.filter.reset') }}
                    </button>
                    <button type="submit" form="filters" class="btn btn-default">
                        <x-orchid-icon class="me-1" path="filter"/> {{ trans('kelnik-estate::admin.filter.apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
