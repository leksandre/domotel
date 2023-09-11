@component($typeForm, get_defined_vars())
    <input type="range" class="form-range" {{ $attributes }} oninput="this.nextElementSibling.innerText = this.value;">
    <span class="kelnik-range-value">{{ $attributes['value'] ?? '' }}</span>
@endcomponent
