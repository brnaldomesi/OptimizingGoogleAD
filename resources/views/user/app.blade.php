
@extends('layouts.app')

@section('pageTitle')
    
@endsection


@section('breadcrumbs')

@endsection

@section('content')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div id="app">
        <app></app>
    </div>

@endsection
