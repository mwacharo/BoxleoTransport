@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <return-component :user="{{ json_encode($user) }}"></return-component>
@endsection
