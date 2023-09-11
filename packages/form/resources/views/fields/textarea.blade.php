<div class="form__input">
    <label for="{{ $id }}" class="input__label">{{ $title }}</label>
    <div class="input">
        <textarea class="input__input" {!! $attributes !!}></textarea>
        <div class="input__error">
            <div class="input__error-text" data-validate-error="{{ $name }}"></div>
        </div>
    </div>
</div>
