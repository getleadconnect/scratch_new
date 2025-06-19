
@isset($user)
  @if($user->vchr_logo !=NULL)  
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url($user->vchr_logo) }}">
  @else

    <link rel="apple-touch-icon" sizes="57x57" href="{{ url('backend/images/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ url('backend/images/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ url('backend/images/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ url('backend/images/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ url('backend/images/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ url('backend/images/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ url('backend/images/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ url('backend/images/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('backend/images/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url('backend/images/favicon/android-icon-192x192.png') }}">
 
  @endif
@endisset
@empty($user)

    <link rel="apple-touch-icon" sizes="57x57" href="{{ url('backend/images/favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ url('backend/images/favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ url('backend/images/favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ url('backend/images/favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ url('backend/images/favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ url('backend/images/favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ url('backend/images/favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ url('backend/images/favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('backend/images/favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url('backend/images/favicon/android-icon-192x192.png') }}">
  

@endempty
    
  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
 
  <link href="{{url('glscratch-web/assets/vendors/general/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{url('glscratch-web/assets/vendors/general/tether/dist/css/tether.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{url('glscratch-web/assets/vendors/general/bootstrap-markdown/css/bootstrap-markdown.min.css')}}" rel="stylesheet"
    type="text/css" />
  <link href="{{url('glscratch-web/assets/vendors/general/animate.css/animate.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{url('glscratch-web/assets/vendors/general/@fortawesome/fontawesome-free/css/all.min.css')}}" rel="stylesheet"
    type="text/css" />
  <link href="{{url('glscratch-web/assets/css/demo2/style.bundle.css')}}" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" type="text/css" href="{{ url('css/jquery.ccpicker1.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ url('backend/css/jquery-ui.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ url('backend/libs/jquery-confirm/jquery-confirm.min.css') }}">
  <style>
  .bg-image{background: url('/glscratch-web/assets/media/logos/tt-bg-2.png') no-repeat center #fff ;
      width: 100%;}.

  .scratchpad {
      width: 300px;
      height: 300px;
      margin: 0 auto;
  }


  .scratchpad img {
    width: 100%;
    max-width: 300px;
    height: auto;
  }
  img.main-logo {
      width: 100%;
  }
</style>
 
