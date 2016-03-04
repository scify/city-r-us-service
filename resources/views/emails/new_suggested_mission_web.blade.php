@extends('template.email')

@section('title')
    Πρόταση Αποστολής
@stop

@section('content')
    <p>Γεια {{ $admin->name }}!</p>

    <p>Ο {{ $name }} <{{ $mail }}> πρότεινε μια αποστολή για την εφαρμογή City-R-Us:</p>


    <p><em>{{ $missionDescription }}</em></p>
@stop