
@extends('layouts.admin')
@section('contenido')
    <h1>BIENVENID@: {{ Auth::user()->name }}</h1>
@endsection