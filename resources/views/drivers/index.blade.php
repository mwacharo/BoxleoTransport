@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <driver-component :user="{{ json_encode($user) }}"></driver-component>
@endsection
