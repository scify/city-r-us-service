@extends('template.email')

@section('title')
    Αλλαγή Κωδικού
@stop

@section('content')
    <p>Αγαπητέ/η {{ $user->name }},</p>

    <p>Ο προσωρινός κωδικός σας για την εφαρμογή City-R-US είναι: <strong>{{ $password }}</strong>.</p>
@stop