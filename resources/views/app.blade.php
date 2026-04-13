<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'StyleU') }}</title>
        
        <!-- OCR A Extended Font -->
        <style>
            @font-face {
                font-family: 'OCR A';
                src: url('/fonts/ocraextended.ttf') format('truetype');
                font-weight: normal;
                font-style: normal;
                font-display: swap;
            }
            
            /* Fallback to Helvetica Mono if custom font fails to load */
            body {
                font-family: 'OCR A', 'Helvetica Mono', monospace;
            }
        </style>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
