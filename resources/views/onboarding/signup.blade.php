@extends('onboarding.layouts.master')
@push('css')
<link href="{{ url('onboarding/assets/css/pages/form/login.css') }}" rel="stylesheet">
<link href="{{ url('backend/fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
<style>
	.field_icon {
		top: 55px !important;
	}
	label.error{
		color: #FF0000;
		font-size: 10px !important;
	}
</style>
@endpush
@section('content')

	<!--begin::Aside-->
		@include('onboarding.layouts.slider')
	<!--begin::Aside-->

	{{-- <!--begin::Content--><div class="help"><a href="#"><span class="mr-2"><img src="{{url('onboarding/assets/media/ep_help.svg')}}"></span>Help center</a></div> --}}

	<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto gsign-up">
		<!--begin::Content body-->
		<div class="d-flex flex-column-fluid flex-center">
			<!--begin::Signin-->
			<div class="login-form login-signin">
				<!--begin::Form-->
				<form class="form" method="post" action="{{url('/user-register') }}" id="kt_sigup_signin_form" autocomplete="off">
					<!--begin::Title-->
					<div class="pb-13 pt-lg-0 pt-5 mx-auto">
						<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">
						<h3 class="sign-title">Start closing more deals
							<br> with <span style="color:#EF233C">Getlead</span></h3>
							<p class="pt-5">Start your 14-day free trial. No credit card required.</p>
						<!-- <p>Create your account</p> -->
					</div>
					<!--begin::Title-->

					<!--begin::Form group-->
					<div class="form-group">
						<label class="sign-label" for="name">Name</label>
						<input class="form-control form-control-solid h-auto" type="text" placeholder="Enter your name" name="name" id="name" autocomplete="off" value="{{ old('name')}}"/>
						@if($errors->has('name'))
							<div class="error arrow">{{ $errors->first('name') }}</div>
						@endif
					</div>
					<!--end::Form group-->

					<!--begin::Form group-->
					<div class="form-group">
						<label class="sign-label" for="email">Email address</label>
						<input class="form-control form-control-solid h-auto" type="text" placeholder="Email address" name="email" id="email" autocomplete="off" value="{{ old('email')}}"/>
						@if($errors->has('email'))
							<div class="error arrow">{{ $errors->first('email') }}</div>
						@endif
					</div>
					<!--end::Form group-->

					<!--begin::Form group-->
					<div class="form-group">
						<div class="form-group form-feild-row  country-code-row text-left position-relative">
							<label class="sign-label" for="phoneField1">Mobile number </label>
							<input class="form-control form-control-solid h-auto phone-field text-input" type="number" placeholder="eg: +91 9447 752 786" name="mobile" id="phoneField1" autocomplete="off" value="{{ old('mobile')}}"/>
							<input type="hidden" id="country_code" value="{{ $countryCode }}">
						</div>
						@if($errors->has('mobile'))
							<div class="error arrow">{{ $errors->first('mobile') }}</div>
						@endif
					</div>
					<!--end::Form group-->

					<!--begin::Form group-->
					<div class="form-group position-relative">
						<div class="d-flex justify-content-between mt-n5">
							<label class="sign-label pt-5" for="password">Password</label>
						</div>
						<input class="form-control form-control-solid h-auto input-lg" type="password" placeholder="Create new password" name="password" autocomplete="off" id="password" />
						<span id="toggle_pwd" class="fa fa-lg fa-fw field_icon fa-eye-slash"></span>
						<div class="pwstrength_viewport_progress"></div>
					</div>
					<div class="form-group position-relative">
						<div class="d-flex justify-content-between mt-n5">
							<label class="sign-label pt-5" for="confirm_password">Confirm Password</label>
						</div>
						<input class="form-control form-control-solid h-auto input-lg" type="password" placeholder="Confirm password " name="confirm_password" autocomplete="off" id="confirm_password" />
						<span id="toggle_cpwd" class="fa fa-lg fa-fw field_icon fa-eye-slash"></span>
						<span class="val-error new-password_confirmation"></span>
						
					</div>
					<!--end::Form group-->

					<!--begin::Action-->
					<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
						<button type="submit" id="kt_sigup_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 sign-btn  d-block w-100 mt-5 mt-5 pt-5">Create account</button>
						<div class="form-check mt-5 mb-5 verify-form">
							{{-- <input class="form-check-input" type="checkbox" value="" name="verify" id="defaultCheck1">
							<label class="form-check-label" for="defaultCheck1">
								<p class="text-left ml-2 pl-2 permission">I don't want to receive emails about Getlead products, best practices, or special offers.</p> --}}
							</label>
						</div>
						<h6 class="have-acc mt-5 pt-5">Already have an account ? <a href="{{route('login')}}"> Login</a></h6>
						<div class="help-mob"><a href="https://getlead.co.uk/help-center/" target="_blank"><span class="mr-2"><img src="{{url('onboarding/assets/media/ep_help.svg')}}"></span>Help center</a></div>
						<p class="terms">By signing up, you agree to our <a href="https://getleadcrm.com/terms_and_conditions/" target="_blank">Terms of Service</a> and<a href="https://www.getlead.co.uk/privacy-policy/" target="_blank"> Privacy Notice.</a> This page is protected by reCAPTCHA 
							and the <a href="https://policies.google.com/privacy" target="_blank">Google Privacy Policy</a> and <a href="https://getleadcrm.com/terms_and_conditions/" target="_blank">Terms of Service</a> apply.</p>
					</div>
					<!--end::Action-->
				</form>
			</div>
		</div>
	</div>
 		
	@push('script')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
	<script src="{{url('onboarding/assets/js/pages/form/register.js')}}"></script>
	<script>
	$(document).ready(function () {
		$(".sign-btn").html('Create account')
		$(document).on('click','.confirm_password', function(){
			$(this).closest('ul').remove();
		});

		$.validator.addMethod("regex", function(value, element, param) {
			return this.optional(element) ||
				value.match(typeof param == "string" ? new RegExp(param) : param);
		}); // Add method to plugin for processing regex……………

		jQuery.validator.addMethod("noSpace", function(value, element) { 
			return value.indexOf(" ") < 0 && value != ""; 
		});

		jQuery.validator.addMethod("wordLowercase", function(value, element) {
			return value.match(/[a-z]/);
		});

		jQuery.validator.addMethod("wordUppercase", function(value, element) {
			return value.match(/[A-Z]/);
		});

		jQuery.validator.addMethod("wordOneSpecialChar", function(value, element) {
			return value.match(/.[!,@,#,$,%,\^,&,*,?,_,~]/);
		});
		jQuery.validator.addMethod("wordOneNumber", function(value, element) {
			return value.match(/\d+/);
		});

        $("#kt_sigup_signin_form").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    noSpace: true
                },
                name: {
                    required: true,
                    regex: "^[a-zA-Z'.\\s]{1,40}$"
                },
                mobile: {
                    required: true,
                    minlength: 7,
                    maxlength: 12,
                    number: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                    wordOneSpecialChar: true,
                    wordUppercase:true,
                    wordLowercase:true,
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
                },
            },
            messages: {
                email: {
                    required: "Email is required",
                    email: "Email must be a valid email address..",
                    noSpace: "Please enter a valid email address sp"
                },
                name:{
                    required : "Please enter your name",
                    regex: "Please enter valid name",
                },
                mobile: {
                    required:"Please enter mobile number",
                    minlength: "Phone number must be of 7 digits",
                    maxlength: "Phone number must be of 12 digits",
                    number: "Phone number must be number",
                },
                password: {
                    required:"Please enter your password",
                    minlength: "Password must be at least 6 characters",
                    wordOneSpecialChar: "Password must contain at least one special character",
                    wordLowercase:"Password must contain lowercase letter",
                    wordUppercase:"Password must contain uppercase letter"
                },
                confirm_password: {
                    required:  "Confirm password is required",
                    equalTo: "Password and confirm password should same"
                }
            },
			submitHandler: function(form) {
				// For example, you can display a success message or make an AJAX request
				$("#loader").show();
				$(".sign-btn").html('<img src="/onboarding/setup/images/loader.gif" alt="getlead" width="25">').attr('disabled','disabled')
				form.submit();
			}
        });
		
	});
	</script>
	@endpush
	<!--end::Page Scripts-->
@endsection