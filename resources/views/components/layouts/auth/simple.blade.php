<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Proman') }}</title>

    <linkpreconnect="https: //fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-start justify-center bg-[#FAFAFA] pt-20">

        <div class="flex flex-col items-center">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-2 mb-2">
                <div class="w-10 h-10 bg-black rounded-lg flex items-center justify-center text-white font-bold text-xl">
                    P
                </div>
                <span class="text-2xl font-bold text-gray-800"></span>
            </a>

            <!-- Auth Content -->
            <div class="w-full sm:max-w-md px-8 py-8 overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

        </div>
    </div>

</body>

</html>