
<form id="formAddGifts" method="POST"  onsubmit="return checkContent();" action="{{route('users.save-gifts')}}" enctype="multipart/form-data">
@csrf					
<div class="card-body card-gift-body">

			<input type="hidden" name="campaign_id" id="campaign_id" value="{{$sof->pk_int_scratch_offers_id}}">
			<input type="hidden" name="offer_type_id" id="offer_type_id" value="{{$sof->type_id}}">
			
			<div class="row" style="border-bottom:1px solid #e4e4e4;">
			<div class="col-lg-6 col-xl-6 col-xxl-6">
				<h6 class="mb-2" style="color:#623737;">Campaign:&nbsp;<span id="add_gift_campaign_name">{{$sof->vchr_scratch_offers_name}}&nbsp;</span></h6>
				<h6 class="mb-2" style="color:#623737;">Type:&nbsp;<span id="add_gift_type_name">{{$sof->type}}</span></h6>
				</div>
				<div class="col-lg-6 col-xl-6 col-xxl-6 text-end">
				<h6 >Scratch Balance: <label class="text-blue">
				<input type="text" name="scratch_balance" id="scratch_balance" style="width:80px;border:none;text-align:right;" readonly value="{{$sbal_count}}">
				/<span>{{$sbal_count}}</span></label> </h6>
			</div>
			</div>
  			
			
			<div class="row mt-3">
				<h6 class="col-12 col-lg-2 col-xl-2 col-xxl-2 col-form-label">Scratch Gift Count</h6>
				<div class="col-12 col-lg-2 col-xl-2 col-xxl-2">
				<input type="number" pattern="[0-9]*" class="form-control offer_count"  name="scratch_gift_count" id="scratch_gift_count" onkeypress="return /[0-9]/i.test(event.key)"/>
				</div>
				<div class="col-12 col-lg-6 col-xl-6 col-xxl-6">
				<button type="button" id="btn-add-gift" class="btn btn-primary "><i class="fa fa-plus"></i>&nbsp;Add Row</button>
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
								<th>Image</th>
								<th></th>
								<th>Status</th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody>
						
							<tr>
								<td class="td-count"><input type="text" pattern="[0-9]*" class="form-control offer_count"  name="offers_count[]" required  readonly  multiple="" value="" onkeypress="return /[0-9]/i.test(event.key)"/></td>
								<td class="td-desc"><textarea class="form-control" name="description[]" required></textarea></td>
								<td><input class="form-control gift-image-file" type="file" name="image_list[]" multiple=true required>	</td>
								<td><img class="gift-image" src="{{url('assets/images/no-image.png')}}" style="width:70px;height:50px;"></td>
								<td>
									<select name="winning_status[]" class="form-control wstatus" required>
									<option value="">--select--</option>
									<option value="1">Win</option>
									<option value="0">Loss</option>
								</td>
								<td>&nbsp;</td>
							</tr>
																
							</tbody>
						</table><!--end /table-->
					</div>

				</div>
				</div>
</div>
	
	<div class="row mt-3 mb-3" style="bottom:120px;margin-right:50px;">
			<div class="col-lg-12 col-xl-12 col-xxl-12 text-end">
			<button class="btn btn-primary btn-xs" type="submit" >Save Gifts</button>
			</div>
	</div>
</form>	

<script>

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
});


</script>