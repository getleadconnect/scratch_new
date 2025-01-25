<form method="POST" action="{{url('users/update-bill')}}" enctype="multipart/form-data">

		@csrf
		
		<input type="hidden"  name="bill_id" value="{{$bill->id}}">
	  <div class="mb-2 row">
			
			<div class="col-lg-11 col-xl-11 col-xxl-11 mt-2">
				<label for="example-text-input" class="col-form-label">Select Offer</label>
				<select class="form-select" name="offer_id_edit" required>
				<option value=''>--select--</option>
				<option value='0'>All</option>
				@foreach($offers as $row)
					<option value='{{$row->pk_int_scratch_offers_id}}' @if($row->pk_int_scratch_offers_id==$bill->offer_id){{__('selected')}}@endif >{{$row->vchr_scratch_offers_name}}</option>
				@endforeach
				</select>
				</div>
										  
			<div class="col-lg-11 col-xl-11 col-xxl-11 mt-2">
				<label for="example-text-input" class="col-form-label">Bill Number</label>
				<input class="form-control" type="number" name="bill_number_edit" value="{{$bill->bill_number}}" required>
				</div>
	
			<div class="col-lg-11 col-xl-11 col-xxl-11 mt-3">
			<button type="submit" id="btnBillSave" class="btn btn-primary">Update Bill </button>
			</div>
	  </div>
	  </form>