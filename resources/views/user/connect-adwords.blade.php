@extends('layouts.public')

@section('content')

<div class="container">
    <div class="row justify-content-center vh-100">
    <div style="
            width: 100%;
            height: 100px;
            text-align: center;
            padding: 60px 0 0px 0;
        ">
            <img src="/assets/img/sidemenu/logo_early_access.png" />
        </div>
        <div class="col-md-8" style="color: white;">

            <h1>Link your Google Ads account.</h1>

            <p>Click "Authorize" to provide AdEvolver with access.
          
          {{ Form::open(['url' =>  'user/connect']) }}

          <div class="form-group" >
            {!! Form::submit('Authorize', ['class'=>'btn btn-primary btn-lg', 'style'=>'background: rgb(202, 45, 120); border-color:rgb(202, 45, 120);']) !!}
          </div>

          {{ Form::close() }}
  
        </div>
    </div>
</div>
@endsection