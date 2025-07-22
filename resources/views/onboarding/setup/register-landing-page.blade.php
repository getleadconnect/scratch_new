@extends('onboarding.setup.layouts.master')
@push('css')

@endpush
@section('content')

<!-- Modal -->
  <div class="modal fade" id="completeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
            <div class="complete-popup">
                <img src="{{url('onboarding/setup/images/pop-frame.png')}}" alt="Getlead">
                <h3>Hey {{auth()->user()->vchr_user_name}}, welcome to Getlead CRM</h3>
                <p class="subhead">Select the membership that best suits you</p>
                <p>With our <span>Trial membership</span>, you can explore the platform with limited features before<br> 
                    committing to a <span>Paid membership</span>. Our Paid membership unlocks the full potential of our<br> platform, providing access to all the features and benefits.</p>
                <div class="payment-btn">
                    <button class="setup-btn become-paid">Become a paid member</button>
                 </div>
                 <div class="payment-btn trial-btn">
                    <button class="setup-btn free-plan">I would like to try it for free</button>
                 </div>
            </div>
        </div>
      </div>
    </div>
  </div>
<input type="hidden" name="" id="rzp-button1">

@push('script')
<script>

    $(document).ready(function () {
        $('#completeModal').modal({backdrop: 'static', keyboard: false}, 'show'); 
         becom_paid = @json(route('setup-pricing'));
         base_url = @json(url('/'));
    });
</script>

<script src="{{url('onboarding/setup/script/script.js')}}"></script>
<script>
  // Apply free plan
  $(document).on('click','.free-plan',function(e){
        $.ajax({
            type: "POST",
            url: base_url+'/setup/apply-free-plan',
            dataType: "json",
            success: function (response) {
                if(response.status == 1){
                    location.href = base_url + '/setup/step-one-add-member/trial_plan';
                    toastr.success('Welcome to getlead crm!');
                }
            }
        });
    });
</script>

@endpush
@endsection