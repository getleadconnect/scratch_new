<!-- <script src="{{url('glscratch-web/assets/vendors/general/jquery/dist/jquery.min.js')}}" type="text/javascript"></script> -->
<script>
    var KTAppOptions = {
      "colors": {
        "state": {
          "brand": "#374afb",
          "light": "#ffffff",
          "dark": "#282a3c",
          "primary": "#5867dd",
          "success": "#34bfa3",
          "info": "#36a3f7",
          "warning": "#ffb822",
          "danger": "#fd3995"
        },
        "base": {
          "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
          "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
        }
      }
    };
  </script>
  <!-- <script type="text/javascript" src="{{ url('backend/js/jquery-3.3.1.min.js')}}"></script> -->
 
  
  <script src="{{url('glscratch-web/assets/vendors/general/jquery/dist/jquery.js')}}" type="text/javascript"></script>
  <script src="{{url('glscratch-web/assets/vendors/general/jquery/dist/jquery-migrate-1.4.1.min.js')}}"></script>
  <script src="{{url('glscratch-web/assets/vendors/general/popper.js/dist/umd/popper.js')}}" type="text/javascript"></script>
  
  <script src="{{url('glscratch-web/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js')}}" type="text/javascript"></script>
  <script src="{{url('glscratch-web/assets/js/demo2/scripts.bundle.js')}}" type="text/javascript"></script>
  <script src="{{   url('glscratch-web/assets/js/demo2/pages/login/login-general.js')   }}" type="text/javascript"></script>
 <!-- <script src="{{url('glscratch-web/assets/js/demo2/pages/login/jq.js')}}" type="text/javascript"></script> -->
  <script src="{{url('glscratch-web/assets/js/demo2/pages/login/scratchie.js')}}" type="text/javascript"></script>
  <script src="{{url('glscratch-web/assets/js/demo2/pages/login/jquery.countdown360.js')}}" type="text/javascript"></script>
<script src="{{url('glscratch-web/assets/js/demo2/pages/login/jquery.countdown360.js')}}" type="text/javascript"></script>
  <script type= "text/javascript" src="{{url('js/jquery-validation/dist/jquery.validate.min.js')}}"></script>
<script type="text/javascript" src="{{url('js/jquery-validation/dist/additional-methods.min.js')}}"></script>
<!-- start -->

<script type="text/javascript" src="{{ url('backend/libs/jquery-confirm/jquery-confirm.min.js') }}"></script>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-131111019-5"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-131111019-5');
</script>

{{-- Clarity --}}
<script type="text/javascript">
  (function(c,l,a,r,i,t,y){
      c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
      t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
      y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
  })(window, document, "clarity", "script", "dgbpmk8h68");
</script>





  <!-- <script type="text/javascript">
    var promoCode = '';
    var bg1 = "{{url('glscratch-web/assets/media/logos/scratch.png')}}";
    var bg2 = "{{url('glscratch-web/assets/media/logos/scratch.png')}}";
    var bg3 = "{{url('glscratch-web/assets/media/logos/betterluck.png')}}";
    var bgArray = [bg1, bg2, bg3],
      selectBG = bgArray[Math.floor(Math.random() * bgArray.length)];
      selectBG= bg1;
      if (selectBG == bg1) {
      promoCode = 'Galaxy chocolate';
    } else if (selectBG == bg2) {
      promoCode = 'Galaxy chocolate';
    }
    if (selectBG == bg3) {
      var promoCode = '';
    }
    $('#promo').wScratchPad({
      // the size of the eraser
      size: 50,
      // the randomized scratch image   
      bg: selectBG,
      // give real-time updates
      realtime: true,
      // The overlay image
      fg: "{{url('glscratch-web/assets/media/logos/scratch-here.png')}}",
      // The cursor (coin) image
      'cursor': 'url("https://jennamolby.com/scratch-and-win/images/coin1.png") 5 5, default',

      scratchMove: function (e, percent) {
        // Show the plain-text promo code and call-to-action when the scratch area is 50% scratched
        if ((percent > 80) && (promoCode != '')) {

          $('.promo-container').show();
          $('body').removeClass('not-selectable');
          $('.promo-code').html('Your gift is: ' + promoCode);
        }
      }
    });
  </script> -->