@includeWhen($buttonTemplate, $buttonTemplate, ['formSlug' => $slug, 'buttonText' => $button_text])
@php
    $pushOnceId = $slug . '-' . $id;
@endphp
@pushonce('footer')
    <template id="{{ $slug }}">
        <div class="form">
            <div id="{{ $slug }}-form" class="form j-form">
                <form action="{{ @route('kelnik.form.submit', ['id' => $id], false) }}" method="post" class="form__form" data-validate="" data-validate-check="key" data-validate-check="submit">
                    @csrf
                    <div style="display: none">
                        <input name="{{ $spamFieldName }}" type="text">
                    </div>
                    <div class="form__title">{{ $title }}</div>
                    <div class="form__caption">{!! $description ?? '' !!}</div>

                    {!! $fields !!}

                    <div class="form__submit">
                        <button type="submit" class="button button_width_block button_size_mega j-form-submit" aria-label="{{ $button_text }}">
                            <span>{{ $button_text }}</span>
                        </button>
                    </div>
                    @if(!empty($policyPageLink))
                        <div class="form__agreement">{!! trans('kelnik-form::front.policyPageLink', ['link' => $policyPageLink]) !!}</div>
                    @endif
                </form>
                <div class="form__response j-form-success">
                    <div class="form__response-heading">{{ $success_title ?? '' }}</div>
                    <div class="form__response-message">{!! $success_text ?? '' !!}</div>
                </div>
                <div class="form__response j-form-error">
                    <div class="form__response-heading">{{ $error_title ?? '' }}</div>
                    <div class="form__response-message">{!! $error_text ?? '' !!}</div>
                </div>
            </div>
        </div>
    </template>
@endpushonce
