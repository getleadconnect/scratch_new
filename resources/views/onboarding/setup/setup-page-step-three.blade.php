@extends('onboarding.setup.layouts.master')
@push('css')

@endpush
@section('content')
<div class="team-dash-sec">

    @include('onboarding.setup.layouts.header')
    
    <div class="add-y-team">
        <div class="row dashboard-row">
            <div class="col-lg-6 col-sm-12 col-md-12">
                <div class="y-team-header">
                    <h3>Add task</h3>
                    <p>To  install the Getlead app, either scan the QR code and download the app or 
                        get a link to your mobile number.</p>
                </div>
                <div class="row finish-setup">
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <div class="login-col">
                            <p> <span>Scan the QR code to download our app</span></p>
                            <img class="qr-code-img" src="{{url('onboarding/setup/images/getlead-qr.png')}}" alt="Getlead" width="130">
                            <p class="d-none">Or</p>
                            <div class="link-phone d-none">
                                <p>Get a link to your mobile number</p>
                                <div class="phone-col">
                                    <select>
                                        <option>+91</option>
                                        <option>+971</option>
                                        <option>+966</option>
                                    </select>
                                    <input type="tel" name="mobile">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <img class="app-img" src="{{url('onboarding/setup/images/android.svg')}}" alt="getlead">
                    </div>
                </div>
                        
                        
            </div>
                <div class="col-lg-6 col-sm-12 col-md-12" ></div>
        </div>
    </div>
    <div class="bottom-btns bottom-btns-v2">
       <div class="back-btn">
          <a href="{{route('setup-add-lead.list')}}">Back</a>
      </div>
       <div class="next-btn">
          <a href="javascript:void(0)" style="padding: 12px" class="send-sms-to-link">
            Let’s start
          </a>
       </div>
    </div>
</div>

@push('script')
<script>
   var url = @JSON(route('send-app-link'));
   var url_billing = @JSON(route('setup-pricing'));
   var redirect_url = @JSON(route('crm.dashboardv3'));
</script>
    
    <script src="{{url('onboarding/setup/script/script.js')}}"></script>
@endpush
@endsection