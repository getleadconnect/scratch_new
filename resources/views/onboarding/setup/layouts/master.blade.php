<!DOCTYPE html>
<html>
   <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Getlead Analtytics Pvt. Ltd. | Connect | Analyse | Convert | Setup Team</title>
        <!-- MOBILE -->
        <meta name='HandheldFriendly' content='true' />
        <meta name='format-detection' content='telephone=no' />
        <meta name="apple-mobile-web-app-title" content=" " />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <!-- / common / -->
        <meta name="author" content="GetLead">
        <meta name="keywords" content="With GetLead, you will never miss a potential lead.
        Gather all insights of your customers and revert at a lightning-fast rate,
        while satisfying the users with customized messages on demand.  " />

        <meta property="og:title" content="GetLead">
        <meta property="og:description" content="With GetLead, you will never miss a potential lead.
        Gather all insights of your customers and revert at a lightning-fast rate,
        while satisfying the users with customized messages on demand. ">
        <meta property="og:image" content="https://getleadcrm.com/resources/images/share.png">
        <meta property="og:url" content="https://getleadcrm.com/">
        <!-- TWITTER  -->
        <meta name="twitter:title" content="GetLead">
        <meta name="twitter:description" content="With GetLead, you will never miss a potential lead.
        Gather all insights of your customers and revert at a lightning-fast rate,
        while satisfying the users with customized messages on demand.">
        <meta name="twitter:image" content="https://getleadcrm.com/resources/images/share.png">
        <meta name="twitter:card" content="summary_large_image">
        <!--  /for analytics/ -->
        <meta property="fb:app_id" content="your_app_id" />
        <meta name="twitter:site" content="@website-username">
        <!-- fav Icon -->
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
        <link rel="icon" type="image/png" sizes="32x32" href="{{ url('backend/images/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ url('backend/images/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ url('backend/images/favicon/favicon-16x16.png') }}">
        <link rel="stylesheet" type="text/css" href="{{url('onboarding/setup/css/bootstrap.min.css')}}">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <!--begin::Page Custom Styles(used by this page)-->
        <link rel="stylesheet" type="text/css" href="{{url('onboarding/setup/css/style.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('onboarding/setup/css/resp.css')}}">
        <!--end::Page Custom Styles-->
         <!-- provide the csrf token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!--end::Layout Themes-->
    <link rel="shortcut icon" href="{{url('onboarding/assets/media/logos/favicon.ico')}}" />
    <link rel="stylesheet" type="text/css" href="{{ url('backend/libs/noty/noty.css') }}">
    <link href="{{ url('css/jquery.ccpicker.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{url('onboarding/setup/css/toastr.min.css')}}">
    @stack('css')
    <style>
        .not-applicable {
            opacity: .4;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <!--begin::Main-->
        @if(session('flash_notification'))
            <input type="hidden" name="" value="{{ session('flash_notification') }}" id="flash-notis">
        @endif
    <!--begin::Content-->
        <div class="setup-teams-sec">
            {{-- Slider page include --}}
            @include('onboarding.setup.layouts.slider')
            
            @yield('content')
        </div>
    <!--end::Content-->
@if (session('success'))      
    <script type="text/javascript">
        toastr.success(@json(@session('success')));
    </script>
@endif

@if (session('error'))      
    <script type="text/javascript">
        toastr.success(@json(@session('error')));
    </script>
@endif
 <!------------------------------------------ end ------------------------------------------------->
<script>
 var asset_url = @json(app()->make('url')->to('onboarding/setup/')); 
 var dataUrls = true;
</script>
 <script src="{{ url('onboarding/setup/script/jquery.min.js') }}"></script>
 <script src="{{ url('onboarding/setup/script/popper.min.js') }}"></script>
 <script src="{{ url('onboarding/setup/script/bootstrap.min.js') }}"></script>
 <script src="{{ url('/js/jquery.ccpicker.js') }}"></script>
 <script type= "text/javascript" src="{{url('js/jquery-validation/dist/jquery.validate.min.js')}}"></script>
 <script src="{{ url('/backend/libs/noty/noty.min.js') }}"></script>
 <script src="{{ url('onboarding/setup/script/toastr.min.js') }}"></script>
 <script>
     var countryCode=$('#country_code').val();
    $("#phoneField1").CcPicker();
    $("#phoneField1").CcPicker("setCountryByCode", countryCode);
 </script>
 @stack('script')
</body>
</html>