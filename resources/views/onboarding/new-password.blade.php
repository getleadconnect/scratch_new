@extends('onboarding.layouts.master')
@push('css')
<link href="{{ url('onboarding/assets/css/pages/form/login.css') }}" rel="stylesheet">
<link href="{{ url('backend/fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<style>
  #toggle_pwd {
 
 top: 45% !important;
 
}</style>
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
					<form class="form" novalidate="novalidate" method="post" action="{{ url('/change-password') }}" id="kt_password_change_form">
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
							{{-- <input class="form-control form-control-solid h-auto" type="password" placeholder="Type your new password" id="password_reg" name="password" autocomplete="off" /> --}}
							<input class="form-control form-control-solid h-auto fa-eye" type="password" placeholder="Type your new password" name="password" autocomplete="off" id="password_reg"/>
							<span id="toggle_pwd" class="fa fa-lg fa-fw field_icon fa-eye-slash mt-5"></span>
						</div>
						<div class="form-group">
							<div class="d-flex justify-content-between mt-n5">
								<label class="sign-label pt-5">Re-enter Password</label>
							</div>
							<input class="form-control form-control-solid h-auto" type="password" placeholder="Re enter password" name="password_confirmation" autocomplete="off" />
						</div>
						<input type="hidden" name="token" value="{{ request()->segment(1) }}"/>
						<!--end::Form group-->
						<!--begin::Action-->
						<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
							<a href="#" id="kt_password_change_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 sign-btn  d-block w-100 mt-5 mt-5 pt-5">Continue</a>
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
	<script src="{{url('onboarding/assets/js/pages/form/forgot.js')}}"></script>
	{{-- <script src="{{url('onboarding/assets/js/pages/form/login.js')}}"></script> --}}
	<script>
		$(function () {
			$("#toggle_pwd").click(function () {
				$(this).toggleClass("fa-eye fa-eye-slash");
			var type = $(this).hasClass("fa-eye") ? "text" : "password";
				$("#password_reg").attr("type", type);
			});
		});
	</script>
@endpush
<!--end::Page Scripts-->
@endsection