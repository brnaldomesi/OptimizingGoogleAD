@extends('layouts.app')

@section('pageTitle')
Insights
@endsection


@section('breadcrumbs')

@endsection

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="app">
  <feed></feed>
</div>


@endsection
