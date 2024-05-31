@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <status-component :user="{{ json_encode($user) }}"></status-component>
@endsection
