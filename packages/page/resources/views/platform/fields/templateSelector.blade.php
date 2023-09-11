@component($typeForm, get_defined_vars())
    <div data-controller="kelnik-template-selector">
        <div class="template-selector__group p-0">
            @foreach($options as $option)
                <label class="template-selector__label @if($active($option->name, $value)) active @endif" data-action="click->kelnik-template-selector#checked">
                    <span class="icon"><img src="{!! $option->iconPath !!}" alt="{{ $option->title }}"></span>
                    <span>
                        <input {{ $attributes->except('id') }} @if($active($option->name, $value)) checked @endif value="{{ $option->name }}" id="{{ $option->name }}-{{$id}}">
                        {{ $option->title }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>
@endcomponent
