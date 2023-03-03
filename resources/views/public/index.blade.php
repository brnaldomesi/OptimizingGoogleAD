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

      <h1 style="margin: 0 0 30px 0;">Login or Register</h1>

      <div class="form-group">
        <a href='../login'><button class='btn btn-primary btn-lg' style='background: rgb(202, 45, 120); border-color:rgb(202, 45, 120);float: left;margin: 0 20px 0 0;'>Login</button></a>
      </div>

      <div class="form-group">
        <a href='../register'><button class='btn btn-primary btn-lg' style='background: rgb(202, 45, 120); border-color:rgb(202, 45, 120);float: left;margin: 0 20px 0 0;'>Register</button></a>
      </div>

    </div>
  </div>


  @endsection