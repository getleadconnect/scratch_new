@extends('onboarding.layouts.master')
@push('css')
<link href="{{ url('onboarding/assets/css/pages/form/login.css') }}" rel="stylesheet">
<link href="{{ url('onboarding/fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<style>
	.field_icon {
		top: 38px !important;
	}
</style>
@endpush
@section('content')
	
	<!--begin::Aside-->
		@include('onboarding.layouts.slider')
	<!--begin::Aside-->

		<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto gsign-up">
			<!--begin::Content body-->
			<div class="d-flex flex-column-fluid flex-center">
				<!--begin::Signin-->
				<div class="login-form login-signin main-login">
					<!--begin::Form-->
					<form class="form" method="post"  action="{{route('user-login')}}"  name="loginForm"> <!--id="kt_login_signin_form"-->
						<!--begin::Title-->
						@csrf
						<div class="pb-13 pt-lg-0 pt-5 mx-auto">
							<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">
						
							<h3 class="sign-title">Login to your account!</h3>
							{{-- <p class="pt-5"> Log in to your account</p> --}}
						</div>
						<!--begin::Title-->
						<!--begin::Form group-->
							<div class="form-group form-feild-row  country-code-row text-left position-relative">
								<label class="sign-label">Mobile number </label>
								<input class="form-control form-control-solid h-auto phone-field text-input" type="number" placeholder="Mobile number" name="mobile" id="phoneField1" autocomplete="off" value="{{ old('mobile')}}" required />
								<input type="hidden" id="country_code" name="country_code" value="{{ $countryCode }}">
							</div>
							<!--end::Form group-->
							<!--begin::Form group-->
							<div class="form-group position-relative">
								<div class="d-flex justify-content-between mt-n5">
									<label class="sign-label pt-5">Password</label>
								</div>
								<input class="form-control form-control-solid h-auto" type="password" placeholder="Type your password here" name="password" autocomplete="off" id="password" required />
								<span id="toggle_pwd" class="fa fa-lg fa-fw field_icon fa-eye-slash mt-5"></span>
								<a href="#" class="text-right text-hover-primary float-right d-block mt-2 forgot-password pt-2" >Forgot Password ?</a>
							</div>
							<!--end::Form group-->
							<!--begin::Action-->
							<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
								<button type="submit"  class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 " style="width:100%;" >Sign In</button> <!--id="kt_login_signin_submit"-->
								<!-- <h6 class="have-acc mt-5 pt-5">Don’t have an account ? <a href="#">Signup</a></h6>	-->
							</div>
							<!--end::Action-->
						
					</form>

			</div>
		</div>
			
	@push('script')
	
	@if(Session::get('success'))
		<script>
			toastr.success("{{Session::get('success')}}");
		</script>
	@endif

	@if (Session::get('error'))
		<script>
			toastr.error("{{Session::get('error')}}");
		</script>
	@endif
	
	@if (Session::get('fp_success'))
		<script>
			toastr.error("{{Session::get('fp_success')}}");
		</script>
	@endif
	
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
		<script src="{{url('onboarding/assets/js/pages/form/login.js')}}"></script>
	@endpush
	<!--end::Page Scripts-->
@endsection
