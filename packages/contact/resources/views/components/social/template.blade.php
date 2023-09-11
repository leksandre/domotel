<ul class="social j-animation__item">
    @foreach($list as $el)
        <li class="social__list">
            <a class="social__link" href="{{ $el->link }}" title="{{ $el->title }}" target="_blank">
                @if($el->iconPath)
                    <img src="{{ $el->iconPath }}" alt="{{ $el->title ?? '' }}" width="32" height="32">
                @elseif($el->iconBody)
                    {!! $el->iconBody !!}
                @endif
            </a>
        </li>
    @endforeach
</ul>
