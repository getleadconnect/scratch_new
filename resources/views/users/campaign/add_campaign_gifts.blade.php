@extends('layouts.master')
@section('title','Dashboard')
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

		<div class="row mt-3 mb-3">	
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6">

		<div class="page-breadcrumb d-none d-lg-flex align-items-center mb-3">
			<div class="breadcrumb-title pe-3">Add Gifts</div>
		</div>
		</div>
		<div class="col-lg-6 col-xl-6 col-xxl-6 col-6 text-right">
				  @php
				  $cid=request()->segment(3);
				  @endphp
                  <a href="{{url()->previous()}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>&nbsp;&nbsp;
				  <a href="{{route('users.campaigns')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>&nbsp;View Campaigns</a>&nbsp;&nbsp;
		</div>
        </div>

			<!--<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">Campaigns Gifts List</div>
             </div> -->
            <!--end breadcrumb-->

              <div class="card">
                <div class="card-header p-y-3">
				<div class="row" >
				<div class="col-lg-6 col-xl-6 col-xxl-6">
					<h6 class="mb-2" style="color:#623737;">Campaign:&nbsp;<span id="add_gift_campaign_name">{{$sof->vchr_scratch_offers_name}}&nbsp;</span></h6>
					<h6 class="mb-2" style="color:#623737;">Type:&nbsp;<span id="add_gift_type_name">{{$sof->type}}</span></h6>
					</div>
					<div class="col-lg-6 col-xl-6 col-xxl-6 text-end">
					<h6 style="color:#623737;">Available Scratch Count: <label class="text-blue">
					<input type="text" name="scratch_balance" id="scratch_balance" style="width:80px;border:none;color:#623737;font-size:20px;" readonly  value="{{$sbal_count??0}}">
					</label> </h6>
				</div>
				</div>
                </div>
                <div class="card-body">
				
				
			<div class="row mt-3">
				<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
				<h6 class="mb-2">Add Gift :</h6>
				
				<form id="formAddGifts"  enctype="multipart/form-data">
				@csrf
								
				<input type="hidden" name="campaign_id" id="campaign_id" value="{{$sof->pk_int_scratch_offers_id}}">
				<input type="hidden" name="offer_type_id" id="offer_type_id" value="{{$sof->type_id}}">
							
				    <div class="table-responsive">
						<table id="giftTable" class="table mb-0">
							<thead class="thead-light">
							<tr>
								<th>Gift_Count</th>
								<th>Description</th>
								<th>Status</th>
								<th>Image(Max :500kb)</th>
								<th></th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody>
						
							<tr>
								<td class="td-count"><input type="text" pattern="[0-9]*" class="form-control" id="offer_count" name="offer_count" required  onkeypress="return /[0-9]/i.test(event.key)"/></td>
								<td class="td-desc"><textarea class="form-control" name="description" required></textarea></td>
								
								<td>
									<select name="winning_status" id="winning_status" class="form-control wstatus" required>
									<option value="">--select--</option>
									<option value="1">Win</option>
									<option value="0">Loss</option>
								</td>
								
								<td>
								<input type="hidden" name="better_luck_image" id="better_luck_image">
								
								<input class="form-control" type="file" id="gift_image" name="gift_image" required>	</td>
								<td><img class="gift-image-output" src="{{url('assets/images/no-image.png')}}" style="width:70px;height:50px;"></td>
								
								<td>
								
								<button class="btn btn-primary btn-xs " id="btn_save_gift"  type="submit" > Add </button>
								&nbsp;</td>
							</tr>
																
							</tbody>
						</table><!--end /table-->
					</div>
					
					</form>

				</div>
				</div>

                   <div class="row mt-3">
                     <div class="col-12 col-lg-12 d-flex">
                      <div class="card  shadow-none w-100">
					  
					  <h6 class="mb-2 mt-2">Gifts List :</h6>
					  
							<div class="table-responsive mt-2">
								<table class="table" id="datatable" style="width:100% !important;">
									<thead class="thead-semi-dark">
									  <tr>
										<th>SlNo</th>
										<th>Image</th>
										<th>Count</th>
										<th>Stage</th>
										<th>Description</th>
										<th>Balance</th>
										<th>Status</th>
										<th>Action</th>
									  </tr>
									</thead>
									<tbody>
																			
									</tbody>
								</table>
                            </div>
				
                       <!-- </div>-->
                      </div> 
                    </div>
                   </div><!--end row-->
                </div>
              </div>
			  
	<form id='foto' method="post" action="{{url('users/update-image')}}" method="POST" enctype="multipart/form-data" >
	@csrf
	    <div style="height:0px;overflow:hidden"> 
        <input type="file" id="picField" name="picField" onchange="this.form.submit()" class="d-none"/> 
        <input type="hidden" id="scrId" name="scrId" /> 
        </div>
      
        <!--<i class='fa fa-camera' onclick="fileInput.click();"></i>-->
    </form> 
			  


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

   $(document).on('click','#imgUpload',function(e){
        $('#picField').trigger('click');
        scrId = $(this).data('id')
        $('#scrId').val(scrId);
    })

BASE_URL ={!! json_encode(url('/')) !!}

 var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
		stateSave:true,
		paging : true,
        pageLength :50,
		scrollX: true,
		
		'pagingType':"simple_numbers",
        'lengthChange': true,

		ajax:
		{
			url:BASE_URL+"/users/view-campaign-gifts",
			data: function (data) 
		    {
               data.campaign_id= $('#campaign_id').val();
		    },
        },

        columns: [
            {"data": 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  },
			{"data": "image" },
			{"data": "int_scratch_offers_count" },
			{"data": "type_name" },
			{"data": "txt_description" },
			{"data": "int_scratch_offers_balance" },
			{"data": "status" },
			{"data": "action" ,name: 'Action',orderable: false, searchable: false },
        ],
			
    });


var addValidator=$('#formAddGifts').validate({ 
	
	rules: {
		offer_count: {required: true},
		description: {required: true},
		image_list:{requirded:true},
		winning_status: {required: true}
	},
});

function clearData()
{
	$("#offer_count").val('');
	$("#description").val('');
	$("gift_image").val('');
	$("winning_status").val('');
}

$("#winning_status").change(function()
{
	var val=$(this).val();
	if(val==0)
	{
		$(".gift-image-output").attr("src","{{url('uploads/offers_listing/better-luck.png')}}");
		$("#better_luck_image").val("better-luck.png");
		$("#gift_image").prop('disabled',true);
		$("#gift_image").prop('required',false);
	}
	else
	{
		$(".gift-image-output").attr("src","{{url('assets/images/no-image.png')}}");
		$("#better_luck_image").val('');
		$("#gift_image").prop('disabled',false);
		$("#gift_image").prop('required',true);	
	}
		
});


$('#formAddGifts').submit(function(e) 
	{
	
	e.preventDefault();
	
	var formData = new FormData(this);
		
	if(parseInt($("#offer_count").val())>0)
	{
		
		var sbal=parseInt($("#scratch_balance").val());
		var scount=parseInt($('#offer_count').val());

		if(sbal<scount)
		{
			alert("Insufficient scratch balance, Balance "+sbal+" only."); 
			$('#offer_count').focus();
		}
		else
		{
			
			var inputVal=$('#giftTable tbody').children('tr:first').find('input').val();
			var inputSel=$('#giftTable tbody').children('tr:first').find('select.wstatus').find(':selected').val();
			
			var inputFile=1;
			
			if($("#winning_status").val()==1)
			{
				inputFile=$('#giftTable tbody').children('tr:first').find('input[type=file]').get(0).files.length;
			}

				if(inputVal!='' && inputFile!=0 && inputSel!='') 
				{
								
					var bal=sbal-scount;
					$("#scratch_balance").val(bal);
					$("#current_balance").html(bal);


					$.ajax({
					url: "{{ url('users/save-gift')}}",
					method: 'post',
					data: formData,
					contentType: false,
					processData: false,
					success: function(result){
						if(result.status == 1)
						{
							
							var bal=sbal-scount;
							$("#scratch_balance").val(bal);
							$("#current_balance").html(bal);
					
							$('#datatable').DataTable().ajax.reload(null,false);
							toastr.success(result.msg);
							$("#formAddGifts")[0].reset();
							$(".gift-image-output").attr("src","{{url('assets/images/no-image.png')}}");
						}
						else
						{
							toastr.error(result.msg);
						}
					}
					
					});
				}
				else
				{	
					alert('Gift details missing');
				}
		}
	}
	else
	{
		alert('Invalid offer count value!');
		$("#offer_count").val('');
	}
});

$('#datatable tbody').on('click','.delete-gift',function()
{
	Swal.fire({
	  //title: "Are you sure?",
	  text: "Are you sure, You want to delete this gift details?",
	  icon: "question",
	  showCancelButton: true,
	  confirmButtonColor: "#3085d6",
	  cancelButtonColor: "#d33",
	  confirmButtonText: "Yes, Delete it!"
	}).then((result) => {
	  if (result.isConfirmed) {
		
		var tid=$(this).attr('id');
		
		  $.ajax({
          url: "{{url('users/delete-gift')}}"+'/'+tid,
          type: 'get',
		  dataType: 'json',
          //data:{'track_id':tid},
          success: function (res) 
		  {
			if(res.status==1)
			{
				 toastr.success(res.msg);
				 $('#datatable').DataTable().ajax.reload(null,false);
				 
				 var sbal=parseInt($("#scratch_balance").val());
				 var cnt=sbal+res.offer_count;
				 $("#scratch_balance").val(cnt);
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
 

$("#giftTable tbody").on('change','#gift_image',function()
{
	var size=$("#gift_image")[0].files[0].size;
	if(size>524288)
	{
		alert("Image size too large. Maximum 500Kb only");
		$(this).val('');
	}
	else
	{
		var img=$(this).closest('tr').find('img.gift-image-output');
		
		var file=this.files[0];
			var allowedExtensions="";
			allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
			var filePath = file.name;
			console.log(file);
		
			if (!allowedExtensions.exec(filePath)) { 
				alert('Invalid file type, Try again.'); 
				$(this).val('');
				img.attr('src','');
			}
			else
			{
				if (file) {
					var reader = new FileReader();
						reader.onload = function (e) {
							img.attr('src',e.target.result);
						}
						reader.readAsDataURL(file);
				  }
			}  
	}
});



</script>
@endpush
@endsection
