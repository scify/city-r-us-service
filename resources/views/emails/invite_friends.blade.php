@extends('template.email')

@section('title', 'Πρόσκληση')

@section('content)
    <p>Ο/Η {{ $user->name }} σας προσκάλεσε στην εφαρμογή City-R-US!</p>

    <p>Το City-R-US ειναι μια εφαρμογή που επιτρέπει στους κατοίκους της Αθήνας να συμμετέχουν σε αποστολές. Επιλέξτε την
        αποστολή που σας ενδιαφέρει και βοηθήστε τη πόλη σας! Τα δεδομένα των αποστολών συλλέγονται σε δημόσιο χάρτη.</p>

    <p>Κατεβάστε την εφαρμογή για κινητό <a href="{{ url('/api/v1/users/invite/clicked?platform=android&token='.$token.'&user_id='.$user->id) }}">Android</a>.
    </p>
@stop