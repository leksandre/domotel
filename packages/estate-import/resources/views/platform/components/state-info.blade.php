@if ($history->state->isNew())
    <h6 class="{{ $stateClass }}">{{ trans('kelnik-estate-import::admin.history.state.new') }}</h6>
@elseif ($history->state->isPreProcessing())
    <h6 class="{{ $stateClass }}">{{ trans('kelnik-estate-import::admin.history.state.pre-process') }}</h6>
    @if(!empty($batch))
        {!! trans(
            'kelnik-estate-import::admin.history.state.processingStat',
            [
                'progress' => $batch->progress(),
                'processed' => $batch->processedJobs(),
                'failed' => $batch->failedJobs,
                'total' => $batch->totalJobs
            ]
        ) !!}
    @endif
    <x-kelnik-estate-import-platform-duration :value="$history" />
    {!! $getLogLinks !!}
@elseif ($history->state->isReady())
    <h6 class="{{ $stateClass }}">{{ trans('kelnik-estate-import::admin.history.state.ready') }}</h6>
    <x-kelnik-estate-import-platform-duration :value="$history" />
    {!! $getLogLinks !!}
@elseif ($history->state->isProcessing())
    <h6 class="{{ $stateClass }}">{{ trans('kelnik-estate-import::admin.history.state.process') }}</h6>
    @if(!empty($batch))
        {!! trans(
            'kelnik-estate-import::admin.history.state.processingStat',
            [
                'progress' => $batch->progress(),
                'processed' => $batch->processedJobs(),
                'failed' => $batch->failedJobs,
                'total' => $batch->totalJobs
            ]
        ) !!}
    @endif
    <x-kelnik-estate-import-platform-duration :value="$history" />
    {!! $getLogLinks !!}
@elseif ($history->state->isDone())
    <h6 class="{{ $stateClass }}">{{ trans('kelnik-estate-import::admin.history.state.done') }}</h6>
    @if(!empty($message))
        <strong class="d-block mb-2">{{ $message }}</strong>
    @endif
    @includeWhen($stat, 'kelnik-estate-import::platform.components.stat', ['stat' => $stat, 'id' => $history->getKey()])
    <x-kelnik-estate-import-platform-duration :value="$history" />
    {!! $getLogLinks !!}
@else
    <h6 class="{{ $stateClass }}">{{ trans('kelnik-estate-import::admin.history.state.error') }}</h6>
    @if(!empty($message))
        <strong>{{ $message }}</strong>
        @if(!empty($filePath))
            <br>{{ $filePath }}
        @endif
    @endif
    <x-kelnik-estate-import-platform-duration :value="$history" />
    {!! $getLogLinks !!}
@endif
