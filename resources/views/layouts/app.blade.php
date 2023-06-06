<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>BIPTIS</title>
  <!-- Favicon -->
  <link href="{{ asset('argon') }}/img/brand/favicon.png" rel="icon" type="image/png">
  <!-- Fonts -->
  <link href="{{ asset('argon') }}/vendor/dataTables/css/fonts.css" rel="stylesheet">
  <!-- datatables -->
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/select.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/fixedHeader.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/fixedColumns.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/select.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/rowGroup.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/rowReorder.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="{{ asset('argon') }}/vendor/dataTables/css/buttons.dataTables.min.css">
  <!-- Icons -->
  <link href="{{ asset('argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
  <link href="{{ asset('argon') }}/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('argon') }}/vendor/jquery ui/css/jquery-ui.css" rel="stylesheet">
  <link href="{{ asset('argon') }}/vendor/gijgo/css/gijgo.min.css" rel="stylesheet">
  <link href="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
  <link type="text/css" href="{{ asset('argon') }}/vendor/bs-stepper/css/bs-stepper.min.css" rel="stylesheet">
  <link type="text/css" href="{{ asset('argon') }}/vendor/toastr/dist/toastr.css" rel="stylesheet">
  <link type="text/css" href="{{ asset('argon') }}/css/argon.css" rel="stylesheet">
  @yield('css')
</head>
<body class="{{ $class ?? '' }}">

  @auth()
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
    @include('layouts.navbars.sidebar')
  @endauth

  <div class="main-content">
    @include('layouts.navbars.navbar')
    @yield('content')
  </div>

  @guest()
    @include('layouts.footers.guest')
  @endguest
  @stack('js' )

  <script src="{{ asset('argon') }}/vendor/jquery/dist/jquery.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/jquery-mask/dist/jquery.mask.js"></script>
  <!-- <script src="{{ asset('sweetalert2') }}/dist/sweetalert2.all.min.js"></script> -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('argon') }}/vendor/jquery ui/js/jquery-ui.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/gijgo/js/gijgo.js"></script>
  <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/bs-stepper/js/bs-stepper.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


  <script type="text/javascript" language="javascript" src="{{ asset('argon') }}/vendor/dataTables/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" language="javascript" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript" language="javascript" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.select.min.js"></script>
  <!-- <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/buttons.bootstrap4.min.js"></script> -->
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.fixedHeader.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.fixedColumns.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.rowGroup.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.rowReorder.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/sum().js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.rowsGroup.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/jszip.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/pdfmake.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/vfs_fonts.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/buttons.html5.min.js"></script>
  <script type="text/javascript" charset="utf8" src="{{ asset('argon') }}/vendor/dataTables/js/buttons.print.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/moment/moment.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/js-cookie/js.cookie.js"></script>
  <script src="{{ asset('argon') }}/vendor/jquery.scrollbar/jquery.scrollbar.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/toastr/dist/toastr.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
  <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
  <script src="{{ asset('argon') }}/js/argon.js?v=1.2.0"></script>

  <script>
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut",
    'timeOut' : 0,
    'extendedTimeOut' : 0,
  };

  $(document).ready(function(){
    $('#whole').bind('DOMMouseScroll mousewheel', function(e){
      if(e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0) {
        alert("up");
      }
      else{
        alert("down");
      }
    });
  });


  </script>
  @yield('javascript')
  @stack('custom-scripts')
</body>
</html>
