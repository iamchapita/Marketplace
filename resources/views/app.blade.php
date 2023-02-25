<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>Marketplace</title>

    @viteReactRefresh
    @vite('resources/js/app.jsx')

</head>

<body>
    <div id="app">
    </div>
</body>

</html>
