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
        
        	<h1>Downloading account data – this won’t take a minute!</h1>
        	<p>You will be redirected automatically.</p>
        	<script>
                
                let polls = 0;

        		pollServer();

        		function pollServer()
				{
                    const user_id = '{{ Auth::user()->id }}'
                    
					setTimeout(function() {

                        console.log(polls)

                        if(polls>30){//after 3 minutes redirect to the error page
                            
                            window.location.replace("{{ url('/registration_error') }}")
                        }
				      
				    $.get(
				    	"{{ url('user/first-run/accounts-downloaded') }}",
				    	"",
				    	function (data){

				    		if ( data == "false" ){
				    			pollServer();

				    		}
				    		else{
								const url = "{{ url('user/accounts/') }}" + '/'+user_id
								window.location.replace(url);
                            }
                            
                            polls++
				    	}


				    	);

					}, 5000);
				}
        	</script>
        </div>
    </div>
</div>


@endsection