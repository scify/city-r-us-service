@extends('template.email')

@section('title', 'Πρόσκληση')

@section('content)
    <p>Click here to reset your password: {{ url('password/reset/'.$token) }}</p>
@stop