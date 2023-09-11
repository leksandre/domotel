@if($item->relationLoaded('children') && $item->children->isNotEmpty())
    @include('kelnik-page::components.header.menu.partial.multilevelMobile-group', ['item' => $item])
@else
    @include('kelnik-page::components.header.menu.partial.multilevelMobile-single', ['item' => $item])
@endif
