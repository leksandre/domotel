@if($item->relationLoaded('children') && $item->children->isNotEmpty())
    @include('kelnik-page::components.header.menu.partial.multilevel-group', ['item' => $item])
@else
    @include('kelnik-page::components.header.menu.partial.multilevel-single', ['item' => $item])
@endif
