@extends('layouts.public')

@section('content')

    <div class="row justify-content-center">
    <div style="
            width: 100%;
            height: 100px;
            text-align: center;
            padding: 60px 0 90px 0;
        ">
            <img src="/assets/img/sidemenu/logo_early_access.png" />
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Register</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Full Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
<!-- 
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div> -->
                        <div class="form-group row">




                            <label for="password" class="col-md-4 col-form-label text-md-right">I agree to the <a href='../terms'>Terms & Conditions</a></label>
                            <div class="col-md-6">
                                <input type="checkbox" class="form-check-input" id="checkbox" class="form-control{{ $errors->has('checkbox') ? ' is-invalid' : '' }}" name="checkbox" value="true" style='margin:15px 0 0 0' required>

                                @if ($errors->has('checkbox'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('checkbox') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" style='background-color: #CA2D78;
    border-color: #CA2D78;'>
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div style='padding:2%; margin: 0 auto;'>
                    <p>To keep your account safe, please choose a strong password:</p>
                    <ul>
                        <li>Minimum 10 characters</li>
                        <li>At least 1 lowercase character</li>
                        <li>At least 1 uppercase character</li>
                        <li>At least 1 special character (e.g. !)</li>
                        <li>At least 1 number</li>
                    </ul>
                    <p>Already have an account? <a href="../login">Login here.</a></p>
                </div>
            </div>

     
        </div>
    </div>
@endsection
