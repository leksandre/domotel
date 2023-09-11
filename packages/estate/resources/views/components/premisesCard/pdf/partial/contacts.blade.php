<section class="pdf-contacts">
    @foreach($contacts as $el)
        <ul class="pdf-contacts__list">
            <li><h4>{{ $el['title'] }}</h4></li>
            <li><b>{{ $el['phone'] }}</b></li>
            <li><span>{{ $el['address'] }}</span></li>
            @if(!empty($el['schedule']))
                <li>
                    @foreach($el['schedule'] as $row)
                        <span>{{ $row['day'] }} {{ $row['time'] }}</span>
                    @endforeach
                </li>
            @endif
        </ul>
    @endforeach
</section>
