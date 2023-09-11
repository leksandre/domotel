<section class="section @isset($margin['top']) section_top_indent-{!! $margin['top'] !!}@endisset @isset($margin['bottom']) section_bottom_indent-{!! $margin['bottom'] !!}@endisset @if(!empty($alias))j-anchor-section" id="{{ $alias }}@endif">
    <div class="visual-inner">
        <iframe class="visual-inner__iframe j-visual-frame" src="{!! $url !!}" frameborder="0"></iframe>
    </div>
</section>
