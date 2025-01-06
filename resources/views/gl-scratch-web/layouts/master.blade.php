<!DOCTYPE html>
<html lang="en">
<!-- begin::Head -->

<head>
  <!--begin::Base Path (base relative path for assets of this page) -->
  <!--<base href="../../../../">-->
  <!--end::Base Path -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
  
  <title> {{ isset($user)?$user->user_name : 'Getlead'}} </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="apple-mobile-web-app-capable" content="yes">
  
  
  <meta name="description" content="Get a Existing offer, click the logo link">
  
  <meta name="robots" content="index">
  <link rel="canonical" href="http://gl1.in">
 
    <meta name="image" content="http://getlead.co.uk/resources/share.png">
    <meta property="og:image" itemprop="image" content="{{url('backend/images/favicon/android-icon-192x192.png')}}"">
    <meta property="og:site_name" content="Getlead">
    <meta property="og:title" content="Getlead- Scratch and Win Gift Cards">    
    <meta name="author" content="Getlead- Scratch and Win Gift Cards">
    
    <meta property="og:description" content="Get a Existing offer, click the logo link" />
    <meta property="og:type" content="website"/>
    <meta property="og:image:type" content="image/jpeg">    
    <meta property="og:url" content="http://gl1.in"> 


  <meta name="description" content="Get a Existing offer, click the logo link">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, shrink-to-fit=no">
 
  
  @include('gl-scratch-web.layouts.styles')  
  <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-144098362-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-144098362-1');
</script>

{{-- Clarity --}}
<script type="text/javascript">
  (function(c,l,a,r,i,t,y){
      c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
      t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
      y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
  })(window, document, "clarity", "script", "dgbpmk8h68");
</script>

</head>
<!-- Metronic Theme-->
<body  class="kt-page--loading-enabled kt-page--loading kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header--minimize-topbar kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-page--loading bg-image" >
  
@yield('content')
@include('gl-scratch-web.layouts.scripts')
@stack('footer.script')
</body>

</html>