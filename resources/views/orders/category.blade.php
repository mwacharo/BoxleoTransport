@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('content')
    <category-component :user="{{ json_encode($user) }}"></category-component>
@endsection
