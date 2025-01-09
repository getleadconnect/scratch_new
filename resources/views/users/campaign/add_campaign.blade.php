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

<!-- for message -------------->
		<input type="hidden" id="view_message" value="{{ Session::get('message') }}">
<!-- for message end-------------->	

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
              <div class="breadcrumb-title pe-3">Add New Campaign</div>
 
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
                  <h6 class="mb-0 pt5">Add Campaign & Gifts</h6>
				  </div>
				  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
                  <a href="{{route('users.campaigns')}}" class="btn btn-primary btn-rect"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
				  </div>
				  				  
				  
				  </div>
                </div>
                <div class="card-body">
				
				<div class="card  shadow-none w-100">
			 
                   <div class="row mt-2">
                     <div class="col-12 col-lg-4 col-xl-4 col-xxl-4" style="border-right:1px solid #e4e4e4;">
                     
                        <!--<div class="card-body">-->
                        <form id="formAddCompaign" method="POST" onsubmit="return checkContent();" action="{{route('users.save-campaign')}}" enctype="multipart/form-data">
						@csrf
						<div class="mb-2 row">
							<div class="col-lg-11 col-xl-11 col-xxl-11">
							<label for="example-text-input" class="col-form-label">Campaign Name</label>
							<input class="form-control" type="text" name="offer_name" id="offer_name" required>
							</div>

							<div class="mt-3 col-lg-9 col-xl-9 col-xxl-9">
								<label for="example-text-input" class="col-form-label">Offer Image(<span class="text-light-blue">Max:500kb</span>)</label>
								<input class="form-control" type="file" name="offer_image" id="offer_image" required>
								</div>
							<div class="mt-3 col-lg-2 col-xl-2 col-xxl-2">
							<img id="img_offer_output" src="{{url('assets/images/no-image.png')}}" style="width:75px;height:70px;">
							</div>
	
							<div class="mt-3 col-lg-9 col-xl-9 col-xxl-9">
								<label for="example-text-input" class="col-form-label">Mobile Image(<span class="text-light-blue">Max:500kb</span>)</label>
								<input class="form-control" type="file" name="mobile_image" id="mobile_image" required>
							</div>
							<div class="mt-3 col-lg-2 col-xl-2 col-xxl-2">
							<img id="img_mobile_output" src="{{url('assets/images/no-image.png')}}" style="width:75px;height:70px;">
							</div>
			
							<div class="mt-3 col-lg-11 col-xl-11 col-xxl-11">
									<label for="example-text-input" class="col-form-label">Campaign Type</label>
									<select class="form-select" name="offer_type" id="offer_type" required>
										<option value="" >--select--</option>
										@foreach($type as $row)
										<option value="{{$row->id}}">{{$row->type}}</option>
										@endforeach
									  </select>
							</div>
							
							
							<div class="mt-3 col-lg-6 col-xl-6 col-xxl-6">
									<label for="example-text-input" class="col-form-label">Campaign End Date</label>
									<input class="form-control" type="date" name="campaign_end_date" id="campaign_end_date" required>
							</div>

						</div>
						</div>
		
						 <div class="col-12 col-lg-8 col-xl-8 col-xxl-8 ps-3">

						<div class="row" style="border-bottom:1px solid #e4e4e4;">
							<h6 class="col-lg-6 col-xl-6 col-xxl-6">Add Gifts</h6>
							
							<div class="col-lg-6 col-xl-6 col-xxl-6 text-end">
							<h6 >Scratch Balance: <label class="text-blue">
							<input type="text" name="scratch_balance" id="scratch_balance" style="width:80px;border:none;text-align:right;" disabled value="{{$sbal_count??0}}">
							/<span>{{$sbal_count??0}}</span></label> </h6>
								
							</div>
						</div>
												
						<div class="row mt-3">
							<h6 class="col-12 col-lg-2 col-xl-2 col-xxl-2 col-form-label">Scratch Gift Count</h6>
							<div class="col-12 col-lg-2 col-xl-2 col-xxl-2">
							<input type="text" pattern="[0-9]*" class="form-control offer_count"  name="scratch_gift_count" id="scratch_gift_count" onkeypress="return /[0-9]/i.test(event.key)"/>
							</div>
							<div class="col-12 col-lg-6 col-xl-6 col-xxl-6">
							<button type="button" id="btn-add-gift" class="btn btn-primary btn-rect mx-3"><i class="fa fa-plus"></i>&nbsp;Add Row</button>
							<span class="fs-7" style="color:#2323f7;" >(Maximum 10 rows only)</span>
							</div>
						</div>
						
						<div class="row mt-3">
						<div class="col-12 col-lg-12 col-xl-12 col-xxl-12">
											
						<input type="hidden" id="initial_tr_value" name="initial_tr_value" value="False">
						
						<div class="table-responsive">
								<table id="giftTable" class="table mb-0">
									<thead class="thead-light">
									<tr>
										<th>Gift_Count</th>
										<th>Description</th>
										<th>Image (<span class="text-light-blue">Max:500kb</span>)</th>
										<th></th>
										<th>Status</th>
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
								
									<tr>
										<td class="td-count"><input type="text" pattern="[0-9]*" class="form-control offer_count"  name="offers_count[]" required readonly multiple="" value="" onkeypress="return /[0-9]/i.test(event.key)"/></td>
										<td class="td-desc"><textarea class="form-control" name="description[]"></textarea></td>
										<td><input class="form-control gift-image-file" type="file" name="image_list[]" multiple=true>	</td>
										<td><img class="gift-image" src="{{url('assets/images/no-image.png')}}" style="width:70px;height:50px;"></td>
										<td>
											<select name="winning_status[]" class="form-control wstatus" required>
											<option value="">--select--</option>
											<option value="1">Win</option>
											<option value="0">Loss</option>
										</td>
										<td></td>
									</tr>
																		
									</tbody>
								</table><!--end /table-->
							</div>

						</div>
						</div>
						</div>
						</div>
						
						
						<div class="row mb-2 mt-5">	
							<div class="col-lg-12 col-xl-12 col-xxl-12 text-end">
							<button class="btn btn-primary btn-rect" type="submit"><i class="lni lni-save"></i>&nbsp;&nbsp;Save Campaign</button>
							</div>
						</div>
						</form>

                       <!-- </div>-->
                      </div> 
                    </div>
                   </div><!--end row-->
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


function checkContent()
{
	var inputVal=$('#giftTable tbody').children('tr:first').find('input').val();
	var inputFile=$('#giftTable tbody').children('tr:first').find('input[type=file]').get(0).files.length;
	var inputSel1=$('#giftTable tbody').children('tr:first').find('select.wstatus').find(':selected').val();
	
	if(inputVal!='' && inputFile!=0 && inputSel1!='')
	{
		return true;
	}
	else
	{
		alert("Gift details missing.");
		return false;
	}
}


$(document).on('click','#btn-add-gift',function(e)
{
	e.preventDefault();
	
	if(parseInt($("#scratch_gift_count").val())>0)
	{
		
		var sbal=parseInt($("#scratch_balance").val());
		var scount=parseInt($('#scratch_gift_count').val());

		if(sbal<scount)
		{
			alert("Insufficient scratch balance, Balance "+sbal+" only."); 
			$('#scratch_gift_count').focus();
		}
		else
		{

		     var offer_fields=`<tr>
					<td class="td-count"><input type="text" pattern="[0-9]*" class="form-control offer_count"  name="offers_count[]" required readonly multiple="" value="" onkeypress="return /[0-9]/i.test(event.key)"/></td>
					<td class="td-desc"><textarea class="form-control" name="description[]"></textarea></td>
					<td><input class="form-control gift-image-file" type="file" name="image_list[]" multiple=true>	</td>
					<td><img class="gift-image" src="{{url('assets/images/no-image.png')}}" style="width:70px;height:50px;"></td>
					<td>
						<select name="winning_status[]" class="form-control wstatus" required>
						<option value="">--select--</option>
						<option value="1">Win</option>
						<option value="0">Loss</option>
					</td>
					<td><button type="button" class="btn btn-outline-secondary btn-action-circle btn-remove-field"><i class="fa fa-minus"></i></button></td>
				</tr>`;

				if($('#giftTable tr').length==2 && $("#initial_tr_value").val()=="False")
				{
					var bal=sbal-scount;
					$("#scratch_balance").val(bal);
					$("#current_balance").html(bal);
					
					$('#giftTable tbody').children('tr:first').find('.offer_count').val($("#scratch_gift_count").val());					
					$("#scratch_gift_count").val('');
					$("#initial_tr_value").val("True");
				}
				else if($("#initial_tr_value").val()=="True")
				{
					var inputVal=$('#giftTable tbody').children('tr:first').find('input').val();
					var inputFile=$('#giftTable tbody').children('tr:first').find('input[type=file]').get(0).files.length;
					var inputSel=$('#giftTable tbody').children('tr:first').find('select.wstatus').find(':selected').val();
			
					if($('#giftTable tr').length>10)
					{
						alert("Can't add rows, Maximum 10 rows only!");
						$("#scratch_gift_count").val('');
					}
					else 
					{
						if(inputVal!='' && inputFile!=0 && inputSel!='')
						{
							
							var bal=sbal-scount;
							$("#scratch_balance").val(bal);
							$("#current_balance").html(bal);
							
							$("#giftTable tbody").prepend(offer_fields);
							$('#giftTable tbody').children('tr:first').find('.offer_count').val($("#scratch_gift_count").val());	
							$("#scratch_gift_count").val('');
						}
						else
						{	
							alert('Gift details missing');
							$("#scratch_gift_count").val('');
						}
					}
				}
		}
	}
	else
	{
		alert("Gift count value missing. Try again");
		$("#scratch_offer_count").focus();
	}

});

$("#giftTable tbody").on('click','.btn-remove-field',function(e)
{

	var gift_count=parseInt($(this).closest('tr').find('input').val());
	var sbal=parseInt($("#scratch_balance").val());
	
		var bal=sbal+gift_count;
		$("#scratch_balance").val(bal);
		$("#current_balance").html(bal);
	
	$(this).closest('tr').remove();
});


$("#giftTable tbody").on('change','.gift-image-file',function()
{
	var img=$(this).closest('tr').find('img.gift-image');
	
	var file=this.files[0];
	var size=this.files[0].size;
	if(size>524288)
	{
		alert("Image size too large. Maximum 500Kb only");
		$(this).val('');
	}
	else
	{
	
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

//500kb images

offer_image.onchange = evt => {
	
	const [file] = offer_image.files
	  
	var size=file.size;
	if(size>524288)
	{
		alert("Image size too large. Maximum 500Kb only");
		$(this).val('');
	}
	else
	{

        var allowedExtensions="";
	    allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
	    var filePath = file.name;
		console.log(file);
	
		if (!allowedExtensions.exec(filePath)) { 
			alert('Invalid file type, Try again.'); 
			$("#offer_image").val('');
			$("#img_offer_output").prop('src','');
		}
		else
		{
			if (file) {
				img_offer_output.src = URL.createObjectURL(file)
			  }
		}  
	}
}


mobile_image.onchange = evt => {
  const [file] = mobile_image.files

	var size=file.size;
	if(size>524288)  
	{
		alert("Image size too large. Maximum 500Kb only");
		$(this).val('');
	}
	else
	{
        var allowedExtensions="";
	    allowedExtensions = /(\.jpg|\.jpeg|\.jpe|\.png)$/i; 
	    var filePath = file.name;
		console.log(file);
	
		if (!allowedExtensions.exec(filePath)) { 
			alert('Invalid file type, Try again.'); 
			$("#mobile_image").val('');
			$("#img_mobile_output").prop('src','');
		}
		else
		{
			if (file) {
				img_mobile_output.src = URL.createObjectURL(file)
			  }
		}  
	}
}

</script>
@endpush
@endsection
