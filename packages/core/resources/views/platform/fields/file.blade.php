@component($typeForm, get_defined_vars())
    <div data-controller="kelnik-file"
         data-kelnik-file-value="{{ $attributes['value'] }}"
         data-kelnik-file-storage="{{ $storage ?? 'public' }}"
         data-kelnik-file-target="{{ $target }}"
         data-kelnik-file-url="{{ $attributes['value'] ? $url : null }}"
         data-kelnik-file-origname="{{ $attributes['origname'] ? $origname : null }}"
         data-kelnik-file-max-file-size="{{ $maxFileSize }}"
         data-kelnik-file-groups="{{$attributes['groups'] ?? ''}}"
         @if($class) class="{{ $class }}" @endif
         @if($style) style="{{ $style }}" @endif
    >
        <div class="border-dashed text-end p-2 picture-actions">
            <div class="fields-file-container"><a href="#" class="file-preview"></a></div>
            <span class="picture-text mt-1 float-start">{{ trans('kelnik-core::admin.upload_file_from_your_pc') }}</span>
            <div class="btn-group">
                <label class="btn btn-default m-0">
                    <x-orchid-icon path="cloud-upload" class="me-2"/>
                    {{ __('Browse') }}
                    <input type="file" data-target="kelnik-file.upload" data-action="change->kelnik-file#upload" class="d-none">
                </label>
                <button type="button" class="btn btn-outline-danger picture-remove" data-action="kelnik-file#clear">{{ __('Remove') }}</button>
            </div>
            <input type="file" class="d-none">
        </div>
        <input class="picture-path d-none" type="text" data-target="kelnik-file.source" {{ $attributes }}>
    </div>
@endcomponent
