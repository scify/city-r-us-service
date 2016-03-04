@extends('template.email')

@section('title')
    Password Reset
@stop

@section('content')
    <p>Click here to reset your password: {{ url('password/reset/'.$token) }}</p>
@stop