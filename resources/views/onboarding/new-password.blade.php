@extends('onboarding.layouts.master')
@push('css')
<link href="{{ url('onboarding/assets/css/pages/form/login.css') }}" rel="stylesheet">
<link href="{{ url('backend/fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<style>
  #toggle_pwd {
 
 top: 45% !important;
  }
 .field_icon {
    position: absolute;
    top: 36% !important;
    right: 2%;
    color: rgba(8, 7, 12, 0.6);
}
</style>
@endpush
@section('content')
	
	<!--begin::Aside-->
		@include('onboarding.layouts.slider')
	<!--begin::Aside-->
		<!--begin::Content-->
		<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto gsign-up">
			<!--begin::Content body-->
			<div class="d-flex flex-column-fluid flex-center">
				<!--begin::Signin-->
				<div class="login-form login-signin">
					<!--begin::Form-->
					<form class="form" novalidate="novalidate" method="post" action="{{ route('update-user-password')}}" id="kt_password_change_form">
					@csrf
					<input type="hidden" name="user_mob" id="user_mob" value="{{$user_mob}}" />
						<!--begin::Title-->
						<div class="pb-13 pt-lg-0 pt-5 mx-auto">
							<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">
							<h3 class="sign-title">Create new password
								</h3>
								{{-- <p class="pt-5">Start your 14-day free trial. No credit card required.</p> --}}
							<!-- <p>Create your account</p> -->
						</div>
						<div class="form-group position-relative">
							<div class="d-flex justify-content-between mt-n5">
								<label class="sign-label pt-5">Enter Password</label>
							</div>
							
							<input type="password" name="password" id="password" class="form-control form-control-solid h-auto fa-eye"  placeholder="Type your new password" minlength=6 maxlength=20 required />
							<span id="toggle_pwd1" class="fa fa-lg fa-fw field_icon fa-eye-slash mt-5"></span>
							<label id="password-error" class="error arrow" for="password"></label>
							</div>
									
						<div class="form-group position-relative">
							<div class="d-flex justify-content-between mt-n5">
								<label class="sign-label pt-5">Re-enter Password</label>
							</div>
							<input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-solid h-auto fa-eye"  placeholder="Type your new password" minlength=6 maxlength=20 required />
							<span id="toggle_pwd2" class="fa fa-lg fa-fw field_icon fa-eye-slash mt-5"></span>
							<label id="password_confirmation-error" class="error arrow" for="password"></label>
						</div>
						
						<!--end::Form group-->
						<!--begin::Action-->
						<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
						 <button type="submit" class="pt-2 pb-2 btn btn-primary font-weight-bolder font-size-h6 px-8 sign-btn d-block w-100 mt-5 mt-5 bold " style="color: #fff !important;font-weight: bold;">CHANGE PASSWORD</button>
							<h6 class="have-acc mt-5 pt-5">Remember password ? <a href="{{url('login')}}"> 
								Back to login page</a></h6>
						</div>
						<!--end::Action-->
					</form>
					<!--end::Form-->
				</div>
			</div>
		</div>
	<!--end::Content-->
	@push('script')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
	<script>
	/* Function for validate form */
 var validate2=$("#kt_password_change_form").validate({
            rules: {
                password:{
                    minlength: 6,
                    maxlength: 20,
                    required: true,
                },
                password_confirmation:{
                    equalTo: "#password",
                }
            },
            messages: {
                password:{
                    required : "Enter valid password",
                },
            },
            errorPlacement: function(label, element) {
                label.addClass("arrow")
                label.insertAfter('#div_pas');
            },
        });


   $(function () {
        $("#toggle_pwd1").click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
        var type = $(this).hasClass("fa-eye") ? "text" : "password";
            $("#password").attr("type", type);
        });
		
		$("#toggle_pwd2").click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
        var type = $(this).hasClass("fa-eye") ? "text" : "password";
            $("#password").attr("type", type);
        });
		
		
    });
	</script>
@endpush
<!--end::Page Scripts-->
@endsection