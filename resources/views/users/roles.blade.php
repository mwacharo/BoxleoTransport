@extends('layouts/contentNavbarLayout')

@section('content')
    <role-component  :user_id="{{ json_encode($user_id) }}"></role-component>
@endsection
