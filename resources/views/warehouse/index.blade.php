@extends('layouts/contentNavbarLayout')

@section('content')
    <Warehouse-component  :user="{{ json_encode($user) }}"></Warehouse-component>
@endsection
