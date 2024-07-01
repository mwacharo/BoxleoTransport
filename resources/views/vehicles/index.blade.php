@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <vehicle-component :user="{{ json_encode($user) }}"></vehicle-component>
@endsection
