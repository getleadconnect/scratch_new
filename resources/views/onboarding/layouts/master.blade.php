<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Getlead Analtytics Pvt. Ltd. | Connect | Analyse | Convert </title>
    <!-- MOBILE -->
    <meta name='HandheldFriendly' content='true' />
    <meta name='format-detection' content='telephone=no' />
    <meta name="apple-mobile-web-app-title" content=" " />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <!-- / common / -->

    <!--begin::Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;400;500;600;700&display=swap" rel="stylesheet">
    <!--end::Fonts-->

    <!--begin::Page Custom Styles(used by this page)-->
    <link rel="stylesheet" href="{{url('onboarding/assets/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{url('onboarding/assets/css/owl.theme.default.min.css')}}">
    <!--end::Page Custom Styles-->

    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{url('onboarding/assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('onboarding/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
    <!--end::Global Theme Styles-->

    <!--end::Layout Themes-->
    <link rel="shortcut icon" href="{{url('onboarding/assets/media/logos/favicon.ico')}}" />
    <link rel="stylesheet" type="text/css" href="{{ url('onboarding/libs/noty/noty.css') }}">
    <link href="{{ url('assets/css/jquery.ccpicker.css') }}" rel="stylesheet">
	 <link href="{{url('assets/plugins/toastr/css/toastr.min.css')}}" rel="stylesheet" />
    @stack('css')
</head>

<body>

    <!--begin::Body-->
	<body id="kt_body" class="header-mobile-fixed subheader-enabled aside-enabled aside-fixed aside-secondary-enabled page-loading">
		<!--begin::Main-->
		@if(session('flash_notification'))
			<input type="hidden" name="" value="{{ session('flash_notification') }}" id="flash-notis">
		@endif
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">

                 @yield('content')

			</div>
            <!--end::Content-->
        </div>
        <!--end::Login-->
    </div>

<!--begin::Global Config(global config for global JS scripts)-->
<!--<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#1BC5BD", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#6993FF", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#1BC5BD", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#E1E9FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>-->
<!--begin::Global Theme Bundle(used by all pages)-->
<script src="{{url('onboarding/assets/plugins/global/plugins.bundle.js')}}"></script>
<!--<script src="https://preview.keenthemes.com/metronic/theme/html/demo3/dist/assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.2.9"></script>-->
<script src="{{url('onboarding/assets/js/scripts.bundle.js')}}"></script>
<script type= "text/javascript" src="{{url('assets/js/jquery-validation/jquery.validate.min.js')}}"></script>
<!--end::Global Theme Bundle-->

<!--begin::Page Scripts(used by this page)-->
<script src="{{url('onboarding/assets/js/owl.carousel.min.js')}}"></script>
<script src="{{ url('onboarding/libs/noty/noty.min.js') }}"></script>
<script src="{{ url('assets/js/jquery.ccpicker.js') }}"></script>
<script src="{{url('assets/plugins/toastr/js/toastr.min.js')}}"></script>
<!--end::Global Config-->

<script>
    //country code
    var countryCode=$('#country_code').val();
    $("#phoneField1").CcPicker();
    $("#phoneField1").CcPicker("setCountryByCode", countryCode);
	
    $('.owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        dots:true,
        autoplay:true,
        autoplayTimeout:3000,
        autoplayHoverPause:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    })
    if($('#flash-notis').val()) {
        flashNotis = $.parseJSON($('#flash-notis').val());
        console.log(flashNotis);
        $.each(flashNotis, function(index, val) {
            //iterate through array or object
            type = val.level;
            status = type.toUpperCase();
            if(status=='DANGER')
            {
                status='ERROR';
            }
            new Noty({
                text: '<strong>' + status + '</strong>! <br> ' + val.message,
                type: type,
                theme: 'relax',
                layout: 'topRight',
                timeout: 3000
            }).show();
        });
    }
</script>
<!--end::Page Scripts-->
@stack('script')
</body>
</html>
