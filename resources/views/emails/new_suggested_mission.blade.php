@extends('emails.email_template')

@section('title', 'Πρόσκληση')

@section('content)
    <p>Γεια {{ $admin->name }}!</p>

    <p>Ο χρήστης {{ $user->name }} πρότεινε μια αποστολή για την εφαρμογή City-R-Us:</p>

    <p><em>{{ $missionDescription }}</em></p>
@endsection