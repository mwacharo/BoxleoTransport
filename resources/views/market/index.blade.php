@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <market-component :user_id="{{ json_encode($user_id) }}"></market-component>

@endsection
