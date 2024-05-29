@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <order-component :user="{{ json_encode($user) }}"></order-component>
@endsection
