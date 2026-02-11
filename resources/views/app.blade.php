<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" value="{{ csrf_token() }}" />
    <title>{{ config('app.name') }}</title>
    <script src="https://accounts.google.com/gsi/client" async></script>
    @vite(['resources/css/app.css','resources/js/app.ts'])
</head>
<body>
    <div id="app"></div> 
</body>
</html>

