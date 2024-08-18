@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <clearance-component :user="{{ json_encode($user) }}"></clearance-component>
@endsection
