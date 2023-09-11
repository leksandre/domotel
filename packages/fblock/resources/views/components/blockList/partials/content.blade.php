<div class="flats-block__content">
    <div class="grid grid_theme_relative">
        <div class="grid__row">
            <div class="grid__common">
                <h2 class="j-animation__header">{{ $title ?? '' }}</h2>
                <div class="flats-block__about">
                    <div class="grid__row j-animation__content">
                        <div class="grid__content grid__content_size_medium">{!! $text !!}</div>
                        @if(!empty($image))
                            <div class="grid__flat-plan j-animation__content-item">
                                <div class="plan">
                                    <div class="plan__inner">
                                        <div class="plan__inner">
                                            <div class="plan__img">
                                                @if(!empty($picture))
                                                    {!! $picture !!}
                                                @else
                                                    <img src="{!! $image->url() !!}" alt="{{ $title }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
