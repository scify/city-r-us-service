@extends('template.email')

@section('title', 'Πρόσκληση')

@section('content)
    <p>Γεια {{ $admin->name }}!</p>

    <p>Ο χρήστης {{ $user->name }} πρότεινε μια αποστολή για την εφαρμογή City-R-Us:</p>

    <p><em>{{ $missionDescription }}</em></p>
@stop