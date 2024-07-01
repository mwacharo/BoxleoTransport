<!DOCTYPE html>

<html class="light-style layout-menu-fixed" data-theme="theme-default" data-assets-path="{{ asset('/assets') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel" data-template="vertical-menu-laravel-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{ env('APP_NAME') }} | Boxleo Transport App </title>
    <!-- laravel CRUD token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Canonical SEO -->
    <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.png') }}" />
    <!-- Include Styles -->
    @include('layouts/sections/styles')

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzbYZ78miZi3hAUmj_HCvpW0mG2VGgxD8"></script>

</head>

<body>
    <div id="app">
        @yield('layoutContent')
    </div>

</body>

</html>
