<a class="d-block pt-2 text-secondary"
   data-bs-toggle="collapse"
   href="#res-stat-{{ $id }}"
   role="button"
   aria-expanded="false"
   aria-controls="res-stat-{{ $id }}">
    <x-orchid-icon path="list" class="me-2" />
    {{ trans('kelnik-estate-import::admin.header.showStat') }}
</a>
<div class="collapse" id="res-stat-{{ $id }}">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>{{ trans('kelnik-estate-import::admin.header.entity') }}</th>
                <th>{{ trans('kelnik-estate-import::admin.header.added') }}</th>
                <th>{{ trans('kelnik-estate-import::admin.header.updated') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $added = 0;
                $updated = 0;
            @endphp
            @foreach($stat as $proxyClass => $data)
                @php
                    $curAdd = (int)\Illuminate\Support\Arr::get($data, 'added', 0);
                    $curUpd = (int)\Illuminate\Support\Arr::get($data, 'updated', 0);
                    $added += $curAdd;
                    $updated += $curUpd;
                @endphp
                <tr>
                    <td>{{ $proxyClass::getTitle() }}</td>
                    <td>{{ $curAdd }}</td>
                    <td>{{ $curUpd }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th scope="col">{{ trans('kelnik-estate-import::admin.header.total') }}</th>
                <td>{{ $added }}</td>
                <td>{{ $updated }}</td>
            </tr>
        </tfoot>
    </table>
</div>
