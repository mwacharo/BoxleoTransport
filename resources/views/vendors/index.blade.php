@extends('layouts/contentNavbarLayout')

@section('content')
    <vendor-component  :user="{{ json_encode($user) }}"></vendor-component>
@endsection
