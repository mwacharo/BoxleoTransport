@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <client-component :user="{{ json_encode($user) }}"></client-component>
@endsection
