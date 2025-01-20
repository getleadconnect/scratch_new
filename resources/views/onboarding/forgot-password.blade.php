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
					<form class="form" method="post"  action="{{route('send-forgot-password-otp')}}"  id="kt_password_change_submit"> <!--id="kt_login_signin_form"-->
						<!--begin::Title-->
						@csrf
						<div class="pb-13 pt-lg-0 pt-5 mx-auto">
							<img class=" logo-mob" src="{{url('onboarding/assets/media/mob-logo.svg')}}" width="80%">
						
							<h3 class="sign-title">Forgot your password!</h3>
							{{-- <p class="pt-5"> Log in to your account</p> --}}
						</div>
						<!--begin::Title-->
						<!--begin::Form group-->
							<div class="form-group form-feild-row  country-code-row text-left position-relative">
								<label class="sign-label">Mobile number </label>
								<input  type="number" name="mobile" id="phoneField1" class="form-control form-control-solid h-auto phone-field text-input"placeholder="Mobile number"  autocomplete="off" value="{{ old('mobile')}}"/>
								<input type="hidden" id="country_code" name="country_code" value="{{ $countryCode }}">
								@if($errors->has('mobile'))
									<label style="color:red;">{{$errors->first('mobile')}}</label>
								@endif
								
								@if($errors->has('fail'))
									<label style="color:red;">{{$errors->first('fail')}}</label>
								@endif
								
							</div>
							<!--end::Form group-->

							<!--begin::Action-->
							<div class="pb-lg-0 pb-5 sign-d-flex  align-items-center w-100 mt-5 pt-5">
								<button type="submit"  class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 " style="width:100%;" >Send Otp</button> <!--id="kt_login_signin_submit"-->
								<p class="have-acc mt-5 pt-5">Remember password ? <a href="{{route('login')}}">Login</a></p>
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

		
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
		
	<script>
	
        var validate = $("#kt_login_signin_form").validate({
            rules: {
                mobile: {
                    required: true,
                    number:true
                },
            },
            messages: {
                mobile:{
                    required : "Enter whatsapp number",
                    number:"Please enter whatsapp number"
                },
            },
            errorPlacement: function(label, element) {
                label.addClass("arrow")
                label.insertAfter(element);
            },
        });

	</script>
	
		
	@endpush
	<!--end::Page Scripts-->
@endsection
