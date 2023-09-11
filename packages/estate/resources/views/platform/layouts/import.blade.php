<fieldset class="mb-3" data-controller="estate-import_plan">
    @empty(!$title)
        <div class="col p-0 px-3"><legend class="text-black">{{ $title }}</legend></div>
    @endempty
    <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
        {!! $form ?? '' !!}
        <div class="d-none table-responsive mt-3" data-estate-import_plan-target="result">
            <div class="kelnik-platform-title">{{ trans('kelnik-estate::admin.import.resultTitle') }}</div>
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-start">{{ trans('kelnik-estate::admin.import.table.type') }}</th>
                        <th class="text-start">{{ trans('kelnik-estate::admin.import.table.file') }}</th>
                        <th class="text-start">{{ trans('kelnik-estate::admin.import.table.result') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</fieldset>
