
@extends('onboarding.layouts.master')
@push('css')
<link href="{{ url('onboarding/assets/css/pages/form/login.css') }}" rel="stylesheet">
<link href="{{ url('backend/fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
@endpush
@section('content')
	
	<!--begin::Aside-->
		@include('onboarding.layouts.slider')
	<!--begin::Aside-->

	{{-- <!--begin::Content--><div class="help"><a href="#"><span class="mr-2"><img src="{{url('onboarding/assets/media/ep_help.svg')}}"></span>Help center</a></div> --}}
		<!--begin::Content-->
		<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto gsign-up">
			<!--begin::Content body-->
			<div class="d-flex flex-column-fluid flex-center">
				<!--begin::Signin-->
				<div class="login-form login-signin">
					<!--begin::Form-->
					<form class="form" novalidate="novalidate" method="POST" action="{{ url('/forgotpassword-email') }}" id="kt_forgot_signin_form">
						<!--begin::Title-->
						<div class="pb-13 pt-lg-0 pt-5 mx-auto">
							<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">
							
							<h3 class="sign-title">Forgot password?</h3>
							<p class="pt-5"> Enter your Email and we will send an otp for verification </p>
							<!-- <p>Create your account</p> -->
						</div>
						{{-- <div class="form-group form-feild-row  country-code-row text-left position-relative">
							<label class="sign-label">Phone number </label>
							<input class="form-control form-control-solid h-auto phone-field text-input" type="text" placeholder="Mobile number" name="number" id="phoneField1" autocomplete="off" value="{{ old('email')}}"/>
							<input type="hidden" id="country_code" value="{{ $countryCode }}">
						</div> --}}
						<div class="form-group">
							<label class="sign-label">Email</label>
							<input class="form-control form-control-solid h-auto" type="text" placeholder="Email address" name="email" autocomplete="off" value="{{ old('email')}}"/>
							@if($errors->has('email'))
								<div class="error arrow">{{ $errors->first('email') }}</div>
							@endif
						</div>
						<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
							<a href="#" id="kt_forgot_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 sign-btn  d-block w-100 mt-5 mt-5 pt-5">Send OTP</a>
							<h6 class="have-acc mt-5 pt-5"> <a href="{{url('login')}}">Back to login page</a></h6>									
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
	@endpush
<!--end::Page Scripts-->
@endsection