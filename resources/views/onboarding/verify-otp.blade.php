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


		<!--begin::Content-->
		<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto gsign-up">
			<!--begin::Content body-->
			<div class="d-flex flex-column-fluid flex-center">
				<!--begin::Signin-->

					<div class="login-form login-signin">
						<!--begin::Form-->
						<form class="form" novalidate="novalidate" method="POST" action="{{ route('check-forgot-password-otp') }}" id="kt_verify_form">
						@csrf
						
						<input type="hidden" name="user_mob" id="user_mob" value="{{$user_mob}}">
																		
							<!--begin::Title-->
							<div class="pb-13 pt-lg-0 pt-5 mx-auto">
								<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">

								<h3 class="sign-title">Kindly confirm the OTP sent to your whatsapp number .</h3>
									<p class="pt-5">We have send one time verification code to  whatsapp number <b> {{ request()->segment(5) }}</b>   <!--<span class="pl-2"><a href="signup.html"><i class="fa fa-edit"></i></a></span>  --></p>
							</div>
							<input type="hidden" name="token" value="{{ request()->segment(2) }}">
							<div class="form-group">
								<div class="d-flex justify-content-between mt-n5">
									<label class="sign-label pt-5">Verify</label>
								</div>
								<input class="form-control form-control-solid h-auto" type="number" placeholder="Enter OTP" name="otp" id="otp" autocomplete="on" pattern="[0-9]*" />
								@if($errors->has('user_otp'))
									<label style="color:red;">{{$errors->first('user_otp')}}</label>
								@endif
								@if($errors->has('fail'))
									<label style="color:red;">{{$errors->first('fail')}}</label>
								@endif
							</div>
							<!--end::Form group-->
							<!--begin::Action-->
							<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
								<button type="submit" class="pt-2 pb-2 btn btn-primary font-weight-bolder font-size-h6 px-8 sign-btn d-block w-100 mt-5 mt-5 bold " style="color: #fff !important;font-weight: bold;">VERIFY</button>
								<p class="have-acc mt-5 pt-5">Remember password ? <a href="{{route('login')}}">Login</a></p>
							</div>
							<!--end::Action-->
						</form>
						
					</div>
			</div>
			<!--end::Content body-->
		</div>
	@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>		
	<script>
	 /* Function for validate form */
    var validate1=$("#kt_verify_form").validate({
            rules: {
                otp: {
                    required: true
                },
            },
            messages: {
                otp:{
                    required : "Enter Valid otp",
                },
            },
            errorPlacement: function(label, element) {
                label.addClass("arrow")
                label.insertAfter(element);
            },
        });
		
	</script>	
		
		
		
		
		
		
		
		
		
		
		
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