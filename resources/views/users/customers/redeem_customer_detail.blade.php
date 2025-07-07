@if($cust)
	
<style>
.tbl-dt tr
{
	line-height:40px;
	font-size:20px;
}
.td-w
{
	width:150px;
}

</style>

	  <div class="card tb_details">
		<div class="card-header p-y-3">
		<div class="row">
		<div class="col-lg-9 col-xl-9 col-xxl-9 col-9">
		   <h6 class="mb-0 pt5 mt-2"><i class="fa fa-user"></i> Customer Details</h6>
		  </div>
		  <div class="col-lg-3 col-xl-3 col-xxl-3 col-3 text-right">
			 <!-- content-->
		  </div>
		  </div>
		</div>

		<div class="card-body ps-5">

		<div class="row">
		<div class="col-12 col-lg-8 col-xl-8 col-xxl-8">

			<table class="tbl-dt mt-3" style="width:100%;">
			<tr><td class="td-w">Campaign </td><td>:&nbsp;<label id="lbl_campaign">{{$cust->vchr_scratch_offers_name}}</label></td></tr>
			<tr><td class="td-w">Name </td><td>:&nbsp;<label id="lbl_name">{{$cust->name}}</label></td></tr>
			<tr><td>Mobile </td><td>:&nbsp;<label id="lbl_mobile">+{{$cust->country_code.$cust->mobile}}</label></td></tr>
			<tr><td>Email </td><td>:&nbsp;<label id="lbl_email">{{$cust->email??"--"}}</label></td></tr>
			<tr><td>Bill No </td><td>:&nbsp;<label id="lbl_bill">{{$cust->bill_no??"--"}}</label></td></tr>
			<tr><td>Branch </td><td>:&nbsp;<label id="lbl_branch" >{{$cust->branch_name}}</label></td></tr>
			<tr><td>Offer </td><td>:&nbsp;<label id="lbl_offer" class="text-light-blue" style="font-size:24px;">{{$cust->offer_text}}</label></td></tr>

			@if($cust->redeem==1)
			<tr><td >Redeemed At</td><td>:&nbsp;{{date_create($cust->updated_at)->format('d-m-Y h:i:s A')}}</td></tr>
			<tr><td colspan=2>
			 <span class="text-red">Customer already redeemed this scratch. Thank You..!</span>
			</td></tr>
			@endif
			<tr><td></td><td><span class="text-green" id="success">&nbsp;<span></td></tr>
			
			<tr><td></td><td>
			@if($cust->redeem!=1 and $cust->win_status==1)
				<button type="submit" class="mt-2 btn btn-primary btn-redeem" id="{{$cust->id}}" style="height:45px;" > Redeem Now</button>
			@endif
			</td></tr>
			<tr><td></td><td><span class="text-green" id="success">&nbsp;<span></td></tr>
			<tr><td colspan=2>&nbsp;</td></tr>
			</table>
			</div>
			<div class="col-12 col-lg-4 col-xl-4 col-xxl-4">
			@if($offer_pic!="")
				<img src="{{url('uploads').'/'.$offer_pic}}" style="margin-top:50px;width:300px;">
			@endif
			</div>
		</div>
		</div>
	  </div>

	@else
	<script>
		Swal.fire({
				text: "Invalid Code, Details not found.!",
				icon: "info"
			  });
	</script>
			
	@endif