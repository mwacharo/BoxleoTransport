@extends('layouts/contentNavbarLayout')

@section('content')
    <product-component  :user="{{ json_encode($user) }}"></product-component>
@endsection
