<!doctype html>
<html lang="en" class="light-theme">

<head>
  <!-- ONDASH Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="{{url('assets/images/favicon-32x32.png')}}" type="image/png" />
  <!--plugins-->
  <link href="{{url('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css')}}" rel="stylesheet"/>
  <link href="{{url('assets/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
  <link href="{{url('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
  <link href="{{url('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
  <link href="{{url('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
  
  <link href="{{url('assets/plugins/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" />
  <link href="{{url('assets/plugins/toastr/css/toastr.min.css')}}" rel="stylesheet" />
  
  <link href="{{url('assets/fontawesome-free-5.15-web/css/all.css')}}" rel="stylesheet" />

   
  <!-- Bootstrap CSS -->
  <link href="{{url('assets/css/bootstrap.min.css')}}" rel="stylesheet" />
  <link href="{{url('assets/css/bootstrap-extended.css')}}" rel="stylesheet" />
  <link href="{{url('assets/css/style.css')}}" rel="stylesheet" />
  <link href="{{url('assets/css/icons.css')}}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <link href="{{url('assets/flaticon2/flaticon.css')}}" rel="stylesheet">
  
  <!-- loader-->
  <link href="{{url('assets/css/pace.min.css')}}" rel="stylesheet" />

  <!--Theme Styles-->
  <link href="{{url('assets/css/dark-theme.css')}}" rel="stylesheet" />
  <link href="{{url('assets/css/light-theme.css')}}" rel="stylesheet" />
  <link href="{{url('assets/css/semi-dark.css')}}" rel="stylesheet" />
  <link href="{{url('assets/css/header-colors.css')}}" rel="stylesheet" />
 
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<link href="{{ url('assets/intl-tel-input17.0.3/intlTelInput.min.css')}}" rel="stylesheet"/>
<link href="{{ url('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet"/>

  <title>GL-Scratch</title>
</head>
<style>
/*pre-loader style*/
.loading-outer{	width:90%;	height:90%;	background:#ffffff9c;position:absolute;}
.loading-inner{	top: 40%;	left: 50%;	transform: translate(-50%, -50%);}
.spinner-loading{text-align:center;}
/*----------------*/
</style>

<body class="pace-done">

  <!--start wrapper-->
  <div class="wrapper toggled">
    <!--start top header-->
      <header class="top-header">        
        <nav class="navbar navbar-expand gap-3">
          <div class="mobile-toggle-icon fs-3 d-flex d-lg-none">
              <i class="bi bi-list"></i>
            </div> 
		  
			<h4>GL-SCRATCH</h4>
            <!--<form class="searchbar">
                <div class="position-absolute top-50 translate-middle-y search-icon ms-3"><i class="bi bi-search"></i></div>
                <input class="form-control" type="text" placeholder="Type here to search">
                <div class="position-absolute top-50 translate-middle-y search-close-icon"><i class="bi bi-x-lg"></i></div>
            </form> -->
            <div class="top-navbar-right ms-auto">
              <ul class="navbar-nav align-items-center gap-1">
			
              </ul>
              </div>
              <div class="dropdown dropdown-user-setting">
                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                  <div class="user-setting d-flex align-items-center gap-2">
				  
					@if(Auth::user()->vchr_logo!="")
						<img src="{{url('uploads').Auth::user()->vchr_logo}}" class="user-img" alt="" >
					@else
						<img src="{{url('assets/images/logos/gl-logo.svg')}}" class="user-img" alt="" >
					@endif
					
                    <div class="d-none d-sm-block">
                       <p class="user-name mb-0">{{Auth::user()->vchr_user_name}}</p>
                    </div>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                   
                    <li>
                      <a class="dropdown-item" href="javacript:void(0)" data-bs-toggle="modal" data-bs-target="#CPassModal">
                         <div class="d-flex align-items-center">
                           <div class=""><i class="bx bx-lock-open-alt"></i></div>
                           <div class="ms-3"><span>Change Password</span></div>
                         </div>
                       </a>
                    </li>
					
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <a href="#"  class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form2').submit();">
                         <div class="d-flex align-items-center">
                           <div class=""><i class="bx bx-log-out"></i></div>
                           <div class="ms-3"><span>Logout</span></div>
                         </div>
                       </a>
					   
					   <form id="logout-form2" action="{{url('logout')}}" method="post" class="d-none">
						@csrf
						</form>
                    </li>
                </ul>
              </div>
        </nav>
      </header>
       <!--end top header-->
	@if(Auth::user()->int_role_id==0)
		@include('layouts.admin_sidebar');
	@elseif(Auth::user()->int_role_id==1)
		@include('layouts.user_sidebar');
	@elseif(Auth::user()->int_role_id==2)
       @include('layouts.user_sidebar');
   @else	
       @include('layouts.shops_sidebar');
   
	@endif
       <!--start content-->
          <main class="page-content">
		  
            @yield('contents')
			
          </main>
       <!--end page main-->

       <!--start overlay-->
        <div class="overlay nav-toggle-icon"></div>
       <!--end overlay-->

       <!--start footer-->
       <footer class="footer">
        <div class="footer-text">
           Copyright ©Getlead Analytics Pvt Ltd, 2024. All right reserved.
        </div>
        </footer>
        <!--end footer-->


			<div class="modal fade" id="CPassModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>

						<div class="modal-body">
						
						<form id="changePass" enctype="multipart/form-data">
						@csrf
						
						<div class="form-group">
							<label>New Password</label>
							<input class="form-control mb-3" type="password" name="new_pass" id="new_pass" placeholder="new password" required>
						</div>
						
						<div class="form-group">
							<label>Confirm Password</label>
							<input class="form-control mb-3" type="password" name="confirm_pass" id="confirm_pass" placeholder="confirm password" required>
						</div>
												
						<label id="cp_err" style="color:red;margin-left:35px;">&nbsp;</label>

						<div class="modal-footer mt-2">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary">Change</button>
						</div>
						</form>
						
						</div>
						
					</div>
				</div>
			</div>
  </div>
  
  <!--end wrapper-->

<script src="{{url('assets/js/jquery.min.js')}}"></script>

  <!-- Bootstrap bundle JS -->
  <script src="{{url('assets/js/bootstrap.bundle.min.js')}}"></script>
  <!--plugins-->
  
  <script src="{{url('assets/plugins/simplebar/js/simplebar.min.js')}}"></script>
  <script src="{{url('assets/plugins/metismenu/js/metisMenu.min.js')}}"></script>
  <script src="{{url('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')}}"></script>
  <script src="{{url('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')}}"></script>
  <script src="{{url('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
  
  <script src="{{url('assets/js/pace.min.js')}}"></script>

  <script src="{{url('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
  <script src="{{url('assets/plugins/toastr/js/toastr.min.js')}}"></script>
  <script src="{{url('assets/plugins/jquery-form/jquery.form.min.js')}}"></script>
    
  <script src="{{url('assets/js/jquery.validation.min.js')}}"></script>
  <script src="{{url('assets/js/moment.js')}}"></script>
  
  <!--app-->
  <script src="{{url('assets/js/app.js')}}"></script>
  
  <script src="{{url('assets/plugins/chartjs/js/Chart.min.js')}}"></script>
  <script src="{{url('assets/plugins/chartjs/js/Chart.extension.js')}}"></script>
   @if(Auth::user()->int_role_id==1)
	   <script src="{{url('assets/plugins/chartjs/js/chartjs-admin-custom.js')}}"></script>
   @else
   <script src="{{url('assets/plugins/chartjs/js/chartjs-custom-user.js')}}"></script>
   @endif
  
  <script src="{{url('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
  <script src="{{url('assets/plugins/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
  <script src="{{url('assets/js/table-datatable.js')}}"></script>
  
  
  <!--<script src="assets/plugins/summernote/summernote-bs4.min.js"></script>-->
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
   
   <script src="{{url('assets/intl-tel-input17.0.3/intlTelInput.min.js')}}"></script>
   <script src="{{ url('assets/plugins/select2/js/select2.min.js')}}"></script>
 <!--<script>
    new PerfectScrollbar(".best-product")
 </script>-->

@stack('scripts')

<script>

$(document).ready(function()
{
	$(".simplebar-wrapper").hover(function()
		{
		$(".wrapper").addClass("sidebar-hovered");
		}, function(){
		$(".wrapper").removeClass("sidebar-hovered");
	});
});

//
$("form#changePass").submit(function(e)
{
   e.preventDefault(); 
   
   var npas=$("#new_pass").val();
   var cpas=$("#confirm_pass").val();
   if(npas!=cpas)
   {
	   $("#cp_err").html("Password does not match!");
   }
   else
   {
	   $("#cp_err").html("&nbsp;");
	  var formData = new FormData(this);
		
       $.ajax({
          url: "{{url('users/change-password')}}",
          type: 'post',
          data: formData,
		  dataType:'json',
          success: function (res) 
		  {
			 if(res.status==true)
			 {
				$('#CPassModal').modal('hide');
				toastr.success(res.msg);
				$('#changePass')[0].reset();
			 }
			 else
			 {
				toastr.error(res.msg); 
				$('#changePass')[0].reset();
		     }
			  			  
          },
			cache: false,
			contentType: false,
			processData: false
		});
   }
});

</script>
</body>

</html>