<footer class="footer">
    <div class="footer__container">
        <div class="footer__inner">
            <div class="footer__developer">
                @if(!empty($logo['path']))
                    @if(!empty($link))<a href="{{ $link }}" target="_blank" class="footer__developer-logo">@endif
                    <img src="{{ $logo['path'] }}" width="{{ $logo['width'] ?? 0 }}" height="{{ $logo['height'] ?? 0 }}" alt="logo">
                    @if(!empty($link))</a>@endif
                @endif
                <p class="footer__developer-copyright">{{ $copyright ?? '' }}</p>
            </div>
            <div class="footer__info" data-nosnippet>
                <!--noindex-->
                @if(!empty($policyLink) && !empty($policyText))
                    <div class="footer__info-links">
                        <a href="{{ $policyLink }}" class="footer__info-link">{{ $policyText }}</a>
                    </div>
                @endif
                <div class="footer__info-disclaimer">{!! $text ?? '' !!}</div>
                <!--/noindex-->
            </div>
            <div class="footer__author">
                <a href="https://multi.kelnik.ru/" target="_blank" class="footer__author-logo">
                    <svg width="72" height="20" viewBox="0 0 72 20" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="multi.kelnik.ru">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.17477 19.6041C3.27787 19.6041 3.36313 19.5237 3.36313 19.4207V0.498299C3.36313 0.395306 3.27787 0.31193 3.17477 0.31193L0.519874 0.317815C0.416771 0.317815 0.333496 0.401191 0.333496 0.503204V19.4217C0.333496 19.5256 0.416771 19.6041 0.519874 19.6041H3.17477ZM11.5374 5.52788C9.80824 7.47462 8.11387 9.38213 7.60034 9.95754C8.13707 10.5435 10.335 12.9505 12.3721 15.1813L12.3739 15.1834C14.3057 17.299 16.0924 19.2555 16.1797 19.3496C16.258 19.4339 16.1668 19.6046 16.0677 19.6046H12.5998C12.5076 19.6046 12.4204 19.5369 12.3391 19.4398L5.08027 11.2062V8.71475L12.3391 0.480157C12.4204 0.383049 12.5076 0.316348 12.5998 0.316348H15.8258C15.9289 0.316348 16.0181 0.482119 15.9437 0.572361C15.8431 0.680558 13.6639 3.13388 11.5374 5.52788ZM27.6351 7.00318C26.4703 5.71625 24.9356 5.11104 22.9113 5.11104C20.8284 5.11104 19.2769 5.72704 18.1001 7.04928C16.9194 8.37643 16.3166 10.2068 16.3166 12.5364C16.3166 14.8895 16.9194 16.7425 18.0793 18.0882C19.2541 19.434 20.7858 20 22.8359 20C24.4608 20 25.8249 19.6616 26.9848 18.7592C28.1219 17.8823 28.8278 16.7405 29.1212 15.3251L29.1252 15.329C29.1263 15.3253 29.1256 15.3218 29.1251 15.3191L29.1248 15.3178C29.1246 15.3167 29.1245 15.3158 29.1247 15.3151L29.1252 15.3143C29.1371 15.1926 29.0112 15.1926 29.0112 15.1926H26.3801C26.1937 15.9175 25.8219 16.5845 25.2687 16.9926C24.7116 17.3957 23.8303 17.6419 23.0233 17.6419C21.8832 17.6419 20.8611 17.2466 20.2346 16.5384C19.607 15.8332 19.1391 14.7208 19.1678 13.4006H29.026C29.2303 13.4006 29.3155 13.2789 29.3284 13.1151C29.3611 12.6747 29.3631 12.5521 29.3631 12.3588C29.3631 10.0891 28.7871 8.30188 27.6351 7.00318ZM20.2772 8.43528C19.6894 9.06403 19.27 10.0508 19.1738 11.224H26.51C26.5031 10.0734 26.1343 9.09542 25.5513 8.45293C24.9615 7.82516 23.9086 7.46419 22.836 7.46419C21.8119 7.46419 20.8612 7.82516 20.2772 8.43528ZM34.7365 19.4207C34.7365 19.5237 34.6512 19.6041 34.5481 19.6041H32.0766C31.9695 19.6041 31.8873 19.5237 31.8873 19.4207V0.498305C31.8873 0.401197 31.9695 0.317821 32.0766 0.317821H34.5481C34.6512 0.317821 34.7365 0.401197 34.7365 0.498305V19.4207ZM61.4815 19.6041C61.5846 19.6041 61.6669 19.5237 61.6669 19.4207V0.509095C61.6669 0.401197 61.5846 0.317821 61.4815 0.317821H59.0071C58.902 0.317821 58.8197 0.401197 58.8197 0.509095V19.4207C58.8197 19.5237 58.902 19.6041 59.0071 19.6041H61.4815ZM65.431 12.3591C66.2485 11.4045 67.8502 9.49743 69.1317 7.97154C70.1362 6.77545 70.9441 5.81356 71.0234 5.72822C71.1007 5.64681 71.0085 5.47123 70.9094 5.47123H68.1573C68.0661 5.47123 67.9789 5.53989 67.8956 5.63602L63.3858 11.1114V13.6058L68.1355 19.4342C68.2158 19.5343 68.304 19.6 68.3933 19.6H71.3832C71.4854 19.6 71.5756 19.4293 71.4973 19.3459C71.3198 19.1527 69.0922 16.7181 65.431 12.3591ZM55.8226 19.6041C55.9266 19.6041 56.0089 19.5237 56.0089 19.4207V5.66956C56.0089 5.56657 55.9266 5.48221 55.8226 5.48221H53.3491C53.246 5.48221 53.1607 5.56657 53.1607 5.66956V19.4207C53.1607 19.5237 53.246 19.6041 53.3491 19.6041H55.8226ZM56.0089 3.01183C56.0089 3.11482 55.9266 3.19624 55.8226 3.19624H53.3491C53.246 3.19624 53.1607 3.11482 53.1607 3.01183V0.508591C53.1607 0.401674 53.246 0.318298 53.3491 0.318298H55.8226C55.9266 0.318298 56.0089 0.401674 56.0089 0.508591V3.01183ZM37.5456 11.5296C37.5456 5.10869 42.3042 5.10869 44.0252 5.10869C45.781 5.10869 50.3512 5.10869 50.3512 11.5296V19.4209C50.3512 19.5239 50.2659 19.6043 50.1628 19.6043H47.6804C47.5793 19.6043 47.495 19.5239 47.495 19.4209V11.2196C47.495 7.57759 44.9085 7.55405 43.9499 7.55405L43.9283 7.55405C42.9304 7.55385 40.4028 7.55334 40.4028 11.2216L40.4067 19.4209C40.4067 19.5239 40.3234 19.6043 40.2203 19.6043H37.732C37.6289 19.6043 37.5456 19.5239 37.5456 19.4209V11.5296Z" fill="#0B1739" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
