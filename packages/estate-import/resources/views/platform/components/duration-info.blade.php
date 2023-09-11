<a class="d-block pt-2 text-secondary"
   data-bs-toggle="collapse"
   href="#res-duration-{{ $history->getKey() }}"
   role="button"
   aria-expanded="false"
   aria-controls="res-duration-{{ $history->getKey() }}">
    <x-orchid-icon path="clock" class="me-2" />
    {{ trans('kelnik-estate-import::admin.header.showDuration') }}
</a>
<div class="collapse mt-2" id="res-duration-{{ $history->getKey() }}">
    <ul>
        @foreach ($history->result as $stepName => $stepData)
            <li class="mb-2">
                <strong class="{{ $getStateClass($stepName) }}">{{ trans('kelnik-estate-import::admin.history.state.' . $stepName) }}</strong>
                @php
                    $startTime = null;
                @endphp

                @if (!empty($stepData['time']['start']))
                    @php
                        $startTime = Illuminate\Support\Carbon::createFromTimestamp($stepData['time']['start']);
                    @endphp
                    <br>{{ trans('kelnik-estate-import::admin.history.state.startAt') }}: {{ $startTime->format('d.m.Y H:i:s') }}
                @endif

                @if ($startTime && !empty($stepData['time']['finish']))
                    <br>{{ trans('kelnik-estate-import::admin.history.state.duration') }}:
                {{ $startTime->diffAsCarbonInterval(Illuminate\Support\Carbon::createFromTimestamp($stepData['time']['finish']))->forHumans(null, true) }}
                @endif
            </li>
        @endforeach
    </ul>
</div>
