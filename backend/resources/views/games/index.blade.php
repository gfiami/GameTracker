<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    @foreach ($games['results'] as $game)
        <div>
            <h2>{{ $game['name'] }}</h2>
            <img src="{{ $game['background_image'] }}" alt="{{ $game['name'] }}">
        </div>
    @endforeach
</body>

</html>
