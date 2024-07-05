@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <fleet-component :user="{{ json_encode($user) }}"></fleet-component>
@endsection
