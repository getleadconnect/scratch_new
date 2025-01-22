@extends('layouts.master')
@section('title','Scratch Bills')
@section('contents')
<style>
.card-body{
	padding-top:2px !important;
}
.td-count
{
	width:10%;
}
.td-desc
{
	width:30%;
}						
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">Staff Users</div>
 
             <!-- <div class="ms-auto">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary">Settings</button>
                  <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">	<a class="dropdown-item" href="javascript:;">Action</a>
                    <a class="dropdown-item" href="javascript:;">Another action</a>
                    <a class="dropdown-item" href="javascript:;">Something else here</a>
                    <div class="dropdown-divider"></div>	<a class="dropdown-item" href="javascript:;">Separated link</a>
                  </div>
                </div>
              </div>  -->
            </div>
            <!--end breadcrumb-->
			
			
			<div class="row">
			<div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
			
              <div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-user-plus"></i> Add User</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				     <!--<a href="javascript:;" class="btn btn-gl-primary" ><i class="lni lni-upload"></i>&nbsp;Export</a>-->
				  </div>
				  </div>
                </div>
                <div class="card-body">

                   <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100 mt-2">

							<form id="formAddStaffUser">
								@csrf
								
								<div class="row mb-2" >
									<div class="col-12 col-lg-11 col-xl-11 col-xxl-11">
										<label for="user_name" class="form-label mb-2">Name<span class="required">*</span></label>
										<input type="text" class="form-control"  name="user_name" id="user_name"  placeholder="Name" required>
									</div>
								</div>

								<div class="row mb-2" >
									<div class="col-12 col-lg-11 col-xl-11 col-xxl-11">
										<label for="mobile" class="form-label mb-2">Mobile<span class="required">*</span></label>
										<input type="hidden" class="form-control" name="country_code" id="country_code"   required>
										<br>
										<input type="tel" class="form-control" name="mobile" id="mobile"  minlength=6 maxlength=15 required>
									</div>
								</div>

								<div class="row mb-2" >
									<div class="col-12 col-lg-11 col-xl-11 col-xxl-11">
										<label for="email" class="form-label mb-2">Email<span class="required">*</span></label>
										<input type="text" class="form-control"  name="email" id="email"  placeholder="Email" required>
									</div>
								</div>
								
								<div class="row mb-2" >
									<div class="col-12 col-lg-11 col-xl-11 col-xxl-11">
										<label for="password" class="form-label mb-2">Password<span class="required">*</span></label>
										<input type="text" class="form-control"  name="password" id="password"  placeholder="password" required>
									</div>
								</div>

								<div class="row mb-2 mt-3">
									<div class="col-lg-11 col-xl-11 col-xxl-11 text-end">
									<button class="btn btn-primary" id="btn-submit" type="submit"> Submit </button>
									</div>
								</div>
								</form>
                    </div>
                   </div><!--end row-->
                </div>
              </div>
		</div>
		</div>
		<div class="col-12 col-lg-8 col-xl-8 col-xxl-8">
		
			<div class="card">
                <div class="card-header p-y-3">
				<div class="row">
				<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
				   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-users"></i> Users List</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				     <!--<a href="javascript:;" class="btn btn-gl-primary" ><i class="lni lni-upload"></i>&nbsp;Export</a>-->
				  </div>
				  </div>
                </div>
                <div class="card-body">

                   <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100 mt-2">
                        <!--<div class="card-body">-->
                          <div class="table-responsive">
	
                             <table id="datatable" class="table align-middle" style="width:100% !important;" >
                               <thead class="thead-semi-dark">
                                 <tr>
									<th>SlNo</th>
									<th>Name</th>
									<th>Country_Code</th>
									<th>Mobile</th>
									<th>Email</th>
									<th>Status</th>
									<th class="no-content" style="width:50px;">Action</th>
								</tr>
                               </thead>
                               <tbody>
                                  
                               </tbody>
                             </table>
                          </div>
						  
						  </div>

                    </div>
                   </div><!--end row-->
                </div>
              </div>
		
		
		</div>
		</div>	  
		
		
		
		
		
		<div class="offcanvas offcanvas-end shadow border-start-0 p-2" id="edit-user" style="width:25% !important" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" aria-modal="true" role="dialog">
          <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Edit</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
          </div>
			<div class="offcanvas-body">
  


  
            </div>
		</div>
	  
	 
			  
		
@push('scripts')


@if(Session::get('success'))
	<script>
		toastr.success("{{Session::get('success')}}");
	</script>
@endif

@if (Session::get('fail'))
	<script>
		toastr.error("{{Session::get('fail')}}");
	</script>
@endif

<script>

BASE_URL ={!! json_encode(url('/')) !!}

var phone_number = window.intlTelInput(document.querySelector("#mobile"), {
	  separateDialCode: true,
	  preferredCountries:["in"],
	  hiddenInput: "full_number",
	  utilsScript:"{{url('assets/intl-tel-input17.0.3/utils.js')}}"
	});


var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging     : true,
        pageLength :50,
		scrollX: true,
		
		'pagingType':"simple_numbers",
        'lengthChange': true,
			
		ajax:
		{
			url:BASE_URL+"/users/view-staff-users",
			data: function (data) 
		    {
               //data.search = $('input[type="search"]').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "name" },
			{"data": "countrycode" },
			{"data": "mobile" },
			{"data": "email" },
			{"data": "status" },
			{"data": "action" ,name: 'Action',orderable: false, searchable: false },
        ],

});


				
var addValidator=$('#formAddStaffUser').validate({ 
	
	rules: {
		user_name: {required: true,},
		email: {required: true,},
		password: {required: true,minlength:6, maxlength:15},
		mobile: {required: true,},
	},

	submitHandler: function(form) 
	{
		var code=phone_number.getSelectedCountryData()['dialCode'];
		$("#country_code").val(code);
		
		$.ajax({
		url: "{{ url('users/save-staff-user') }}",
		method: 'post',
		data: $('#formAddStaffUser').serialize(),
		success: function(result){
			if(result.status == 1)
			{
				$("#btn-submit").attr('disabled',false).html('Submit')
				$('#datatable').DataTable().ajax.reload(null,false);
				toastr.success(result.msg);
				$('#formAddStaffUser')[0].reset();
			}
			else
			{
				toastr.error(result.msg);
			}
		}
		});
	  }
	});



$('#datatable tbody').on('click','.delete-user',function()
{
	Swal.fire({
	  //title: "Are you sure?",
	  text: "Are you sure, You want to delete this user?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, Delete it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		var tid=$(this).attr('id');
		
		  $.ajax({
          url: "{{url('users/delete-staff-user')}}"+'/'+tid,
          type: 'get',
		  dataType: 'json',
          //data:{'track_id':tid},
          success: function (res) 
		  {
			if(res.status==1)
			{
				 toastr.success(res.msg);
				 $("#datatable").DataTable().ajax.reload(null,false);
			}
			else
			{
				 toastr.error(res.msg);
			}
          }
		});

	  }
	});

});

$('#datatable tbody').on('click','.edit-user',function()
{

	var id=$(this).attr('id');
	var Result=$("#edit-user .offcanvas-body");

			jQuery.ajax({
			type: "GET",
			url: "{{url('users/edit-staff-user')}}"+"/"+id,
			dataType: 'html',
			//data: {vid: vid},
			success: function(res)
			{
			   Result.html(res);
			}
		});
});



$("#datatable tbody").on('click','.btn-act-deact',function()
{
	var opt=$(this).data('option');
	var id=$(this).attr('id');
	
	var opt_text=(opt==1)?"activate":"deactivate";
	optText=opt_text.charAt(0).toUpperCase()+opt_text.slice(1);
	
	Swal.fire({
	  title: optText+"?",
	  text: "You want to "+opt_text+" this bill?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, "+opt_text+" it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		
		  jQuery.ajax({
			type: "get",
			url: BASE_URL+"/users/act-deact-staff-user/"+opt+"/"+id,
			dataType: 'json',
			//data: {vid: vid},
			success: function(res)
			{
			   if(res.status==true)
			   {
				   toastr.success(res.msg);
				   $('#datatable').DataTable().ajax.reload(null, false);
			   }
			   else
			   {
				 toastr.error(res.msg); 
			   }
			}
		  });
	  }
	});

});


</script>
@endpush
@endsection
