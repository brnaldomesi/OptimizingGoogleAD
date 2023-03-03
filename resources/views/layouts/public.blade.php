<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <link rel="icon" href="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-32x32.png" sizes="32x32" />
    <link rel="icon" href="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-192x192.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-180x180.png" />
    <meta name="msapplication-TileImage" content="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-270x270.png" />
    
    <title>AdEvolver</title>
    
    <link href="{{ asset('assets/lib/perfect-scrollbar/css/perfect-scrollbar.min.css') }}" rel="stylesheet">
    
    <link href="{{ asset('assets/lib/material-design-icons/css/material-design-iconic-font.min.css') }}" rel="stylesheet">


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!--@todo use minified css -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Hotjar Tracking Code for app.adevolver.com -->
<script>
(function(h,o,t,j,a,r){
h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
h._hjSettings={hjid:1799623,hjsv:6};
a=o.getElementsByTagName('head')[0];
r=o.createElement('script');r.async=1;
r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
a.appendChild(r);
})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>

    @yield ('addAccountGraphsJavascript')

  </head>
  <body style='height:100%; background-color: rgb(17, 20, 46);'>
    <div class="be-wrapper" id="app">
      <nav class="navbar navbar-expand fixed-top be-top-header">
        <div class="container-fluid">
          
          <div class="be-right-navbar">
            <ul class="nav navbar-nav float-right be-user-nav">
              
            </ul>
            <div class="page-title"></div>
          </div>
        </div>
      </nav>
      <div class="be-content">
        
        <div class="main-content container-fluid">
          @yield('content')
        </div>
      </div>
    </div>
    
    <script src="{{ asset('assets/lib/jquery/jquery.min.js') }}"></script>
    
    <script src="{{ asset('assets/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') }}"></script>
    
    <script src="{{ asset('assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    
    <script src="{{ asset('assets/lib/plotly/plotly-latest.min.js') }}"></script>

    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script type="text/javascript">
      $(document).ready(function(){
      	//initialize the javascript
      });
      
    </script>
    <script defer src="https://use.fontawesome.com/releases/v5.1.1/js/all.js" integrity="sha384-BtvRZcyfv4r0x/phJt9Y9HhnN5ur1Z+kZbKVgzVBAlQZX4jvAuImlIz+bG7TS00a" crossorigin="anonymous"></script>
   

    <!-- development version, includes helpful console warnings -->
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>  
  </body>
</html>