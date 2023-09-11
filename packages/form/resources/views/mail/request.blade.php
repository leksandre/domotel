<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
</head>
<body>
    <h2>{{ $header }}</h2>
    <table>
    @foreach($fields as $field)
        <tr><td>{{ $field['title'] }}:</td><td>{{ $field['value'] }}</td></tr>
    @endforeach
    </table>

    @if(!empty($link))
        <p>{{ trans('kelnik-form::mail.referer') }}: <a href="{{ $link }}">{{ $link }}</a></p>
    @endif

    <p>{{ trans('kelnik-form::mail.created') }}: {{ $date }}</p>
</body>
</html>
