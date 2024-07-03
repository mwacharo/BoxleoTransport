@extends('layouts/contentNavbarLayout')

@section('content')
    <Zone-component  :user="{{ json_encode($user) }}"></Zone-component>
@endsection
