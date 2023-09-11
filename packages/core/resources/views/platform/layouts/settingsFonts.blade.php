<fieldset class="mb-3" data-async>
    <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
        <p>@lang('kelnik-core::admin.settings.fonts.text')</p>
        <p><strong>@lang('kelnik-core::admin.settings.fonts.subhead')</strong></p>
        <div class="container settings-fonts mb-2">
            @foreach($fonts as $fontKey => $font)
                <div class="row p-2 font-element">
                    <div class="col-1">
                        <strong>{{ \Illuminate\Support\Str::ucfirst($fontKey) }}</strong>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-8 font-name">
                                @if(!$font->isLoaded())
                                    <input id="fonts_regular_file" type="file" name="fonts[{{ $fontKey }}]" class="form-control" @if($accept)accept="{{ $accept }}"@endif>
                                @else
                                    <x-orchid-icon path="paper-clip"/> {{ $font->getFileName() }}
                                @endif
                            </div>
                            @if($font->isLoaded())
                                <div class="col-2 form-check">
                                    <input class="form-check-input mt-2" type="checkbox" name="fonts[{{ $fontKey }}][active]" value="1" id="fonts_{{ $fontKey }}_active" @if($font->isActive()) checked @endif>
                                    <label class="form-check-label" for="fonts_{{ $fontKey }}_active">@lang('kelnik-core::admin.settings.fonts.active')</label>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($font->isLoaded())
                        <div class="col-1 col-md-auto">
                            <input type="hidden" name="fonts[{{ $fontKey }}][delete]" value="0">
                            <a href="javascript:;" onclick="this.previousElementSibling.value=1; this.closest('fieldset').querySelector('button[type=submit]').click();"><x-orchid-icon path="trash"/></a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        {!! $form ?? '' !!}
    </div>
</fieldset>
