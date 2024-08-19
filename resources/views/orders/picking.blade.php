@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <picking-component :user="{{ json_encode($user) }}"></picking-component>
@endsection
