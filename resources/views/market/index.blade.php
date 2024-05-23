@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <market-component :user="{{ json_encode($user) }}"></market-component>
@endsection
