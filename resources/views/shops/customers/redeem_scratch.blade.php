@extends('layouts.master')
@section('title','Gl-links')
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
	  <div class="breadcrumb-title pe-3">Redeem Scratch</div>

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

	  <div class="card">
		<div class="card-header p-y-3">
		<div class="row">
		<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
		   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-users"></i> Search </h6>
		  </div>
		  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
				<!-- content --->
		  </div>
		  </div>
		</div>
		<div class="card-body">

				<div class="row">
				  <div class="col-lg-12 col-xl-12 col-xxl-12">
				  <form id="searchCustomer" >
				  @csrf
					  <div class="row mt-3" style="padding:3px 10px 10px 10px;" >
						<div class="col-12 col-lg-3 col-xl-3 col-xxl-3">
							<input class="form-control" name="code_mobile" id="code_mobile" style="padding-left:20px;height:45px;font-size:20px !important;" type="text" placeholder="Unique Id" >
						</div>
						<div class="col-12 col-lg-6 col-xl-6 col-xxl-6">
							<button type="submit" class="btn btn-primary sbtn" style="width:110px;" > <i class="bi bi-search"></i>&nbsp;&nbsp;Search</button>
							<button type="button" class="btn btn-secondary sbtn ms-3" id="btn_clear" > Clear</button>
							<button type="button" class="btn btn-secondary sbtn ms-3 btn-camera" ><i class="fa fa-camera" style="font-size:25px;"></i></button>
						</div>
					   </div>

				   </form>
				   					   
					   <div class="row mt-3" style="padding:3px 10px 10px 10px;" >
						<div class="col-12 col-lg-3 col-xl-3 col-xxl-3">
						<div id="qr-reader" style="width: 100%; height: 100%;"></div>
						</div>
						
										
					   </div>
						<button type="button" class="btn-primary btn-xs btn-sm ms-2" id="stop-scanner" style="width:130px;display:none;">Stop Scanner</button>		
					   
				  
				  </div>
				</div>
		</div>
	  </div>
	  
  <div class="container">
  <div class=" align-items-center">
	  <div class="customer_details">
	 
	  </div>
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

<script src="{{url('assets/js/html5-qrcode.js')}}"></script>

<script>

$(document).on('click','.btn-camera', function()
{
        // Initialize the QR code scanner
        const html5QrCode = new Html5Qrcode("qr-reader");
		$("#stop-scanner").css('display','block');
        // Start scanning the camera feed
        html5QrCode.start(
            { facingMode: "environment" }, // Use rear camera (mobile)
            {
                fps: 10, // Frame rate for scanning
                qrbox: 250, // Box size for QR scan area
            },
            (decodedText, decodedResult) => {
                // This function will be called when a QR code is scanned
                document.getElementById("code_mobile").value=decodedText;
				$("#stop-scanner").css('display','none');
				html5QrCode.stop();
				$("form#searchCustomer").submit();
            },
            (errorMessage) => {
                // This function is called on error
                console.log(errorMessage);
            }
        ).catch((err) => {
            console.log("Error starting QR scanner: " + err);
        });

        // Stop scanner when button is clicked
        document.getElementById("stop-scanner").addEventListener("click", () => {
            html5QrCode.stop().then(() => {
                console.log("Scanner stopped.");
				$("#stop-scanner").css('display','none');
            }).catch((err) => {
                console.log("Error stopping scanner: " + err);
            });
        });
});
    </script>


<script>

BASE_URL ={!! json_encode(url('/')) !!}


$("#btn_clear").click(function()
{
	$(".customer_details").html('');
	$("#code_mobile").val('');
});


	$("form#searchCustomer").submit(function (event)
	{
       event.preventDefault();
		   var formData= new FormData(this);
           var url = BASE_URL + '/shops/redeem-scratch-now';
				$.ajax({
				url: url,
				method: 'post',
				data: formData,
				contentType: false,
				processData: false,
				success: function(res)
				{
					$(".customer_details").html(res);
				}
				
				});
    });
	
	
	   
	$(document).on('click', '.btn-redeem', function (event) {
           event.preventDefault();
           var cid = $(this).attr('id');

			Swal.fire({
				  //title: "Are you sure?",
				  text: "Are you sure, You want to redeem now?",
				  icon: "question",
				  showCancelButton: true,
				  confirmButtonColor: "#3085d6",
				  cancelButtonColor: "#d33",
				  confirmButtonText: "Yes, Redeem it!"
				}).then((result) => {
				  if (result.isConfirmed) {
						
					$.ajax({
					  url: "{{url('shops/scratch-redeem')}}" +"/"+cid,
					  type: 'get',
					  dataType: 'json',
					  //data:{'track_id':tid},
					  success: function (res) 
					  {
						if(res.status==true)
						{
							$("#success").html("Redeem SUCCESS!!!");
							$(".btn-redeem").css('display','none');
							$("#code_mobile").val('');
							
							Swal.fire({
								  title: "SUCCESS!!!",
								  text: "Redeem successfully completed",
								  icon: "success"
							  });
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
