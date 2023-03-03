
@extends('layouts.app')

@section('pageTitle')
Budget Commander
@endsection


@section('breadcrumbs')

@endsection

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="app">
  <budget-commander :account="'{{ $account->id }}'"></budget-commander>
</div>

@endsection
