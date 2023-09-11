@component($typeForm, get_defined_vars())
    <div data-controller="kelnik-quill"
         data-kelnik-quill-toolbar='@json($toolbar)'
         data-kelnik-quill-base64='@json($base64)'
         data-kelnik-quill-value='@json($value)'
         data-kelnik-quill-groups="{{$attributes['groups'] ?? ''}}"
         data-theme="{{$theme ?? 'inlite'}}"
    >
        <div id="kelnik-quill-toolbar-{{$id}}"></div>
        <div class="quill p-3 position-relative" id="kelnik-quill-wrapper-{{$id}}" style="min-height: {{ $attributes['height'] }}"></div>
        <input class="d-none" {{ $attributes }}>
        <div id="kelnik-quill-source-{{$id}}" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Html') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"><textarea class="kelnik-quill-source"></textarea></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-default kelnik-quill-source-save">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcomponent
