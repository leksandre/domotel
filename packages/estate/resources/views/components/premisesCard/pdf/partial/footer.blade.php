<footer class="pdf-footer">
    <div class="pdf-footer__items">
        <div class="pdf-footer__item"><span>{!! $url ?? '' !!}</span></div>
        <div class="pdf-footer__item"><span>{{ trans('kelnik-estate::front.components.premisesCard.pdf.created') }} {{ $createDateTime }}</span></div>
        <div class="pdf-footer__item"><span>{{ trans('kelnik-estate::front.components.premisesCard.pdf.noOffer') }}</span></div>
    </div>
</footer>
