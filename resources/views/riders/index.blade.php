@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <rider-component :user="{{ json_encode($user) }}"></rider-component>
@endsection
