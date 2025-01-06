@extends('onboarding.layouts.master')
@push('css')
<link href="{{ url('onboarding/assets/css/pages/form/login.css') }}" rel="stylesheet">
<link href="{{ url('backend/fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<style>
	.disabled-link {
		pointer-events: none;
		cursor: not-allowed; /* Optional: Change the cursor to indicate it's not clickable */
		text-decoration: none; /* Optional: Remove underline or other link styling */
		color: #999; /* Optional: Change the color to indicate it's disabled */
	}
	#timer{
		font-size: 14px;
    	font-weight: 600;
	}
</style>
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
				@if(request()->segment(4) == 'verify')
					<div class="login-form login-signin">
						<!--begin::Form-->
						<form class="form" novalidate="novalidate" method="POST" action="{{ url('/verify-mobile') }}" id="kt_verify_signin_form">
							<!--begin::Title-->
							<div class="pb-13 pt-lg-0 pt-5 mx-auto">
								<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">
								{{-- <h3 class="sign-title">Kindly confirm the OTP sent to your email or phone number.</h3>
									<p class="pt-5">We have send one time verification code to the mobile or email <b>+{{ (request()->segment(3)) ?? '' }} or {{ request()->segment(5) }}</b>   <!--<span class="pl-2"><a href="signup.html"><i class="fa fa-edit"></i></a></span>  --></p> --}}
								<h3 class="sign-title">Kindly confirm the OTP sent to your email .</h3>
									<p class="pt-5">We have send one time verification code to  email <b> {{ request()->segment(5) }}</b>   <!--<span class="pl-2"><a href="signup.html"><i class="fa fa-edit"></i></a></span>  --></p>
							</div>
							<input type="hidden" name="token" value="{{ request()->segment(2) }}">
							<div class="form-group">
								<div class="d-flex justify-content-between mt-n5">
									<label class="sign-label pt-5">Verify</label>
								</div>
								<input class="form-control form-control-solid h-auto" type="number" placeholder="Enter OTP" name="otp" autocomplete="on" pattern="[0-9]*" />
							</div>
							<!--end::Form group-->
							<!--begin::Action-->
							<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
								<a href="#" id="kt_verify_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 sign-btn  d-block w-100 mt-5 mt-5 pt-5">Verify</a>
							</div>
							<!--end::Action-->
						</form>
						<form action="{{route('resend-otp')}}" method="POST" id="formResend" class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
							<input type="hidden" name="mob" class="mob" value="{{ request()->segment(3) }}">
							<input type="hidden" name="email" class="email" value="{{ request()->segment(5) }}">
							<input type="hidden" name="tokens" value="{{ request()->segment(2) }}">
							<input type="hidden" name="page" class="page" value="register">
							<h6 class="have-acc">Didn't get OTP ? <a class="resendButton" href="javascript:{}" onclick="resendOtp()">Resend</a> <span class="ml-4" id="timer"></span></h6>
						</form>
						<!--end::Form-->
					</div>
				@else
					<div class="login-form login-signin">
						<!--begin::Form-->
						<form class="form" novalidate="novalidate" method="POST" action="{{ url('forgot-password-verification') }}" id="kt_verify_forgot_signin_form">
							<!--begin::Title-->
							<div class="pb-13 pt-lg-0 pt-5 mx-auto">
								<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">
								<h3 class="sign-title">Verify your email address</h3>
									<p class="pt-5">We have send one time verification code to this mail id <b>{{ (request()->segment(3)) ?? '' }}</b>   <!--<span class="pl-2"><a href="signup.html"><i class="fa fa-edit"></i></a></span>  --></p>
							</div>
							<input type="hidden" name="token" class="token" value="{{ request()->segment(2) }}">
							<input type="hidden" name="email" class="phone_number" value="{{ request()->segment(3) }}">
							<div class="form-group">
								<div class="d-flex justify-content-between mt-n5">
									<label class="sign-label pt-5">Verify</label>
								</div>
								<input class="form-control form-control-solid h-auto" type="number" placeholder="Enter OTP" name="otp" autocomplete="on" pattern="[0-9]*" />
							</div>
							<!--end::Form group-->
							<!--begin::Action-->
							<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
								<a href="#" id="kt_verify_forgot_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 sign-btn  d-block w-100 mt-5 mt-5 pt-5">Verify Otp</a>
								
							</div>
						</form>
						<form action="{{route('resend-otp')}}" method="POST" id="formResend" class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
							<input type="hidden" name="mob" class="mob" value="{{ request()->segment(3) }}">
							<input type="hidden" name="page" class="page" value="forgot">
							<h6 class="have-acc">Didn't get OTP ? <a class="resendButton" href="javascript:{}" onclick="resendOtp()">Resend</a> <span class="ml-4" id="timerEm"> </h6>
						</form>
						<div class="text-center mt-4">
							<a href="{{url('/login')}}"><small>&nbsp; &larr; Back to login</small></a>
						</div>
						<!--end::Form-->
					</div>
				@endif	
			</div>
			<!--end::Content body-->
		</div>
	@push('script')
		<script src="{{url('onboarding/assets/js/pages/form/verify.js')}}"></script>
		<script>
			$(document).ready(function() {
				@if (session('resend_otp'))
					startTimer();
				@endif
			});
		</script>
		<script>
			function resendOtp(){
				document.getElementById('formResend').submit();
			}
			let countdown; // Stores the interval ID for the countdown timer

			function startTimer() {
				const resendButton = $(".resendButton");
				const timer = $("#timer");
				const timerEm = $("#timerEm");
				let seconds = 60; // Number of seconds for the timer

				// Disable the resend button during the countdown
				resendButton.addClass("disabled-link");

				// Update the timer text and start the countdown
				updateTimerDisplay(seconds);

				countdown = setInterval(() => {
					seconds--;

					if (seconds === 0) {
						// Enable the resend button when the countdown is complete
						resendButton.removeClass('disabled-link');
						clearInterval(countdown);
					}

					updateTimerDisplay(seconds);
				}, 1000);
			}

			function updateTimerDisplay(seconds) {
				const minutes = Math.floor(seconds / 60);
				const remainingSeconds = seconds % 60;
				const timer = $("#timer");
				const timerEm = $("#timerEm");

				// Format the timer display as "MM:SS"
				timer.text(`${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`);
				timerEm.text(`${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`);
			}
		</script>
	@endpush
<!--end::Page Scripts-->
@endsection