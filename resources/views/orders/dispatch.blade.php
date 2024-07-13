@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <dispatch-component :user="{{ json_encode($user) }}"></dispatch-component>
@endsection
