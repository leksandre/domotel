<section class="pdf-about">
    <h2>{{ $about['title'] ?? '' }}</h2>
    <div class="pdf-about__address">
        <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M0 6.63755C0 2.97186 3.13386 0 7 0C10.8661 0 14 2.9716 14 6.63729C14 10.303 7 18 7 18C7 18 0 10.3032 0 6.63755ZM4.375 6.54546C4.375 7.90112 5.55019 9 7 9C8.45021 9 9.625 7.90112 9.625 6.54546C9.625 5.18979 8.45021 4.09091 7 4.09091C5.55019 4.09091 4.375 5.18979 4.375 6.54546Z" />
        </svg>
        @if(!empty($about['address']))
            <span>{{ $about['address'] }}</span>
        @endif
    </div>
    @if(!empty($about['images']))
        <div class="pdf-about__images">
            @foreach($about['images'] as $el)
            <img class="pdf-about__images-item" src="{!! $el !!}" alt="">
            @endforeach
        </div>
    @endif
    <p class="pdf__text">{!! ltrim(rtrim($about['text'] ?? '', '</p>'), '<p>') !!}</p>
    @if (!empty($about['utp']))
        <div class="pdf-details">
            <ul class="pdf-details__list">
                @foreach($about['utp'] as $el)
                    @if(($loop->index + 1) % 4 === 0)
                        </ul></div>
                        <div class="pdf-details"><ul class="pdf-details__list">
                    @endif
                    <li>
                        <div class="pdf-details__heading">{{ $el['title'] ?? '' }}:</div>
                        <b>{{ $el['text'] ?? '' }}</b>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>
