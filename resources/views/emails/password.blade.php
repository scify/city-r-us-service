@extends('emails.email_template')

@section('title', 'Πρόσκληση')

@section('content)
    <p>Click here to reset your password: {{ url('password/reset/'.$token) }}</p>
@endsection