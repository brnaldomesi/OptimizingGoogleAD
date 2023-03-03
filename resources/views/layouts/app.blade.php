<!DOCTYPE html>
<html lang="en" style="font-family: 'Titillium Web', sans-serif;
                       font-size: 14px;">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  @yield('scripts')
  <link href="https://fonts.googleapis.com/css?family=Titillium+Web&display=swap" rel="stylesheet">
  <link href="{{ mix('assets/css/app.css') }}" rel="stylesheet">
  <meta name="user-id" content="{{ Auth::user()->id }}">
  <meta name="account-id" content="{{ Auth::user()->current_account_id }}">
  <meta name="user-img-url" content="{{ Auth::user()->google_image_url }}">

  <link rel="icon" href="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-32x32.png" sizes="32x32" />
    <link rel="icon" href="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-192x192.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-180x180.png" />
    <meta name="msapplication-TileImage" content="https://adevolver.com/wp-content/uploads/2018/04/cropped-adevo-fav-270x270.png" />

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

    <title>AdEvolver</title>
</head>
<body>

          @yield('pageTitle')

{{--          @yield('breadcrumbs')--}}
          @yield('content')



    <script src="{{ asset('assets/lib/jquery/jquery.min.js') }}"></script>
{{--    <script src="{{ asset('assets/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js') }}"></script>--}}
{{--    <script src="{{ asset('assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>--}}


    @include('layouts.javascript')
    @yield ('addAccountGraphsJavascript')
    @yield('javascript')

    <script src="{{ asset('assets/js/app.js') }}" type="text/javascript"></script>
{{--    <script src="{{ asset('assets/js/bootstrap.js') }}" type="text/javascript"></script>  --}}

    @if (config('app.env') == 'local')
      <script src="http://localhost:35729/livereload.js"></script>
    @endif

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/5f10765da45e787d128b9169/default';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    <!--End of Tawk.to Script-->
    
  </body>
</html>