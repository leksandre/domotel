<li class="navigation-mobile__list-item">
    <ul class="navigation-mobile__list navigation-mobile__list_theme_nested j-navigation-mobile__menu">
        <li class="navigation-mobile__list-item">
            <button class="navigation-mobile__button j-navigation-mobile__menu-forward">
                @if($item->iconBody)
                    {!! $item->iconBody !!}
                @elseif($item->icon->exists)
                    <img src="{!! $item->icon->url !!}" alt="{{ $item->title }}" width="24" height="24" />
                @endif
                {{ $item->title }}
                <svg class="navigation-mobile__list-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.29298 16.7071C8.90246 16.3166 8.90246 15.6834 9.29298 15.2929L12.5859 12L9.29298 8.70711C8.90246 8.31658 8.90246 7.68342 9.29298 7.29289C9.68351 6.90237 10.3167 6.90237 10.7072 7.29289L15.4143 12L10.7072 16.7071C10.3167 17.0976 9.68351 17.0976 9.29298 16.7071Z" />
                </svg>
            </button>
        </li>
        <li class="navigation-mobile navigation-mobile__list-nested j-navigation-mobile__menu">
            <button class="navigation-mobile__button navigation-mobile__button-back j-navigation-mobile__menu-back">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.707 16.7071C15.0975 16.3166 15.0975 15.6834 14.707 15.2929L11.4141 12L14.707 8.70711C15.0975 8.31658 15.0975 7.68342 14.707 7.29289C14.3165 6.90237 13.6833 6.90237 13.2928 7.29289L8.5857 12L13.2928 16.7071C13.6833 17.0976 14.3165 17.0976 14.707 16.7071Z" />
                </svg>
                {{ trans('kelnik-menu::front.back') }}
            </button>

            <ul class="navigation-mobile__list j-navigation-mobile__menu">
                @foreach($item->children as $subItem)
                    @include('kelnik-page::components.header.menu.partial.multilevelMobile-el', ['item' => $subItem])
                @endforeach
            </ul>
        </li>
    </ul>
</li>
