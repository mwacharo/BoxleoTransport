@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <ordermap-component :user="{{ json_encode($user) }}"></ordermap-Component>
@endsection
