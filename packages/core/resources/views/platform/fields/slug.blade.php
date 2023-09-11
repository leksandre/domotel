@component($typeForm, get_defined_vars())
    <div data-controller="slug"
         data-slug-source="{{$source}}"
         data-slug-method="{{$actionUrl}}"
         data-slug-source-id="{{$sourceId}}"
         data-slug-additional-fields="{{$additionalFields}}"
    >
        <div class="input-group input-append-slug">
            <input {{$attributes}} data-action="keyup->slug#onKeyUp">
            <div class="input-group-append">
                <a href="javascript:;" data-action="click->slug#onChange" class="slug-link">
                    <span class="input-group-text"><x-orchid-icon path="link" class="" /></span>
                </a>
            </div>
        </div>
    </div>
@endcomponent
