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
        
        	<h1>Registration Error</h1>
        	<p>We're sorry, but there was a problem downloading your Google Ads accounts.</p>
        	<p>Possible reasons include:</p>
            <ul>
                <li>The account you linked doesn't have any Google Ads accounts</li>
                <li>Your Google Ads accounts have already been registered under a different AdEvolver account</li>
            </ul>
            <p>Please<a style="color:#CA2D78" href="mailto:support@adevolver.com"> email our support team</a> (support@adevolver.com) and we'll have you up and running in no time.</p>
        	
        </div>
    </div>
</div>


@endsection