<fieldset>
    @empty(!$title)
        <div class="col p-0 px-3">
            <legend class="text-black">{{ $title }}</legend>
        </div>
    @endempty
</fieldset>

@if($accordion)
    <div id="accordion-{{\Illuminate\Support\Str::slug($title)}}" class="accordion kelnik-accordion mb-3">
        @foreach($groups as $group)
            @php
                $slug = md5($title . '|' . $group->title);
            @endphp
            <div class="accordion-heading collapsed bg-white rounded shadow-sm mb-1"
                 id="heading-{{$slug}}"
                 data-bs-toggle="collapse"
                 data-bs-target="#collapse-{{$slug}}"
                 aria-expanded="true"
                 aria-controls="collapse-{{$slug}}">
                <h6 class="btn btn-link btn-group-justified pt-0 pb-0 mb-0 pe-0 ps-0 d-flex align-items-center">
                    <x-orchid-icon path="bs.caret-up-fill" class="kelnik-accordion-arrow small"/> {!! $group->title !!}
                </h6>
            </div>

            <div id="collapse-{{$slug}}"
                 class="mt-2 pl-3 collapse"
                 aria-labelledby="heading-{{$slug}}"
                 data-bs-parent="#accordion-{{\Illuminate\Support\Str::slug($title)}}">
                @php
                    /** @var \Illuminate\Support\Collection $rows */
                    $elements = $rows->filter(
                        static fn(\Kelnik\EstateVisual\Models\StepElement $row) => in_array($row->estate_model_id, $group->elementIds) && $row->parent_id === $group->visualElementId
                    )->sort(static fn($a, $b) => $a->title <=> $b->title);
                @endphp
                @include('kelnik-estate-visual::platform.layouts.partial.step-table', ['rows' => $elements])
            </div>
        @endforeach
    </div>
@else
    @include('kelnik-estate-visual::platform.layouts.partial.step-table')
@endif
