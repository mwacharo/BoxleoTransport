@extends('layouts/contentNavbarLayout')

@section('content')
    <permission-component  :user_id="{{ json_encode($user_id) }}"></permission-component>
@endsection
