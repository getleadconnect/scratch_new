<form id="formUpdateUser">
			@csrf
			
			<input type="hidden" name="user_id" value="{{$usr->pk_int_user_id}}">
			
			<div class="row mb-2" >
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
					<label class="form-label">Name<span class="required">*</span></label>
					<input type="text" class="form-control"  name="user_name_edit" id="user_name_edit" value="{{$usr->vchr_user_name}}" placeholder="Name" required>
				</div>
			</div>

			<div class="row mb-2" >
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
					<label  class="form-label">Mobile<span class="required">*</span></label>
					<input type="hidden" class="form-control" name="country_code_edit" id="country_code_edit" value="+{{$usr->countrycode}}"  required>
					<br>
					<input type="tel" class="form-control" name="mobile_edit" id="mobile_edit" value="+{{$usr->countrycode.$usr->mobile}}" minlength=6 maxlength=15 required>
				</div>
			</div>

			<div class="row mb-2" >
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
					<label class="form-label">Email<span class="required">*</span></label>
					<input type="text" class="form-control"  name="email_edit" id="email_edit" value="{{$usr->email}}" placeholder="Email" required>
				</div>
			</div>
			
			<div class="row mb-2" >
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
					<label class="form-label">Compnay/Shop Name<span class="required">*</span></label>
					<input type="text" class="form-control"  name="company_edit" id="company_edit" value="{{$usr->company_name}}" placeholder="Company/shop Name" required>
				</div>
			</div>
			
			<div class="row mb-2" >
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
					<label class="form-label">Designation<span class="required">*</span></label>
					<input type="text" class="form-control"  name="designation_edit" id="designation_edit" value="{{$usr->designation_id}}" placeholder="Designation" required>
				</div>
			</div>

			<div class="row mb-2" >
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
					<label class="form-label">Location<span class="required">*</span></label>
					<input type="text" class="form-control"  name="location_edit" id="location_edit" value="{{$usr->location}}" placeholder="Location" required>
				</div>
			</div>

			<div class="row mb-2" >
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
					<label class="form-label">Address</label>
					<textarea rows=3  class="form-control"  name="address_edit" id="address_edit" placeholder="Address">{{$usr->address}}</textarea>
				</div>
			</div>

			<div class="row mb-2">
				<div class="col-lg-12 col-xl-12 col-xxl-12 text-end">
				<button type="button" class="btn btn-danger btn-offcanvas-close" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
				<button class="btn btn-primary" id="btn-submit" type="submit"> Update </button>
				</div>
			</div>
			</form>
			

<script>
var phone_number = window.intlTelInput(document.querySelector("#mobile_edit"), {
	  separateDialCode: true,
	  preferredCountries:["in"],
	  hiddenInput: "full_number",
	  utilsScript:"{{url('assets/intl-tel-input17.0.3/utils.js')}}"
	});

	
				
var addValidator=$('#formUpdateUser').validate({ 
	
	rules: {
		user_name: {required: true,},
		email: {required: true,},
		location: {required: true,},
		password: {required: true,minlength:6, maxlength:15},
		mobile: {required: true,},
	},

	submitHandler: function(form) 
	{
		//$("#btn-submit").attr('disabled',true).html('Saving <i class="fa fa-spinner fa-spin"></i>')
		
		var code=phone_number.getSelectedCountryData()['dialCode'];
		$("#country_code_edit").val(code);
		
		$.ajax({
		url: "{{ url('admin/update-user') }}",
		method: 'post',
		data: $('#formUpdateUser').serialize(),
		success: function(result){
			if(result.status == 1)
			{
				$('#datatable').DataTable().ajax.reload(null,false);
				toastr.success(result.msg);
				$('#formUpdateUser')[0].reset();
				$('#mobile_edit').val('');
			}
			else
			{
				toastr.error(result.msg);
			}
		}
		});
	  }
	});



</script>
