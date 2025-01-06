@extends('onboarding.setup.layouts.master')
@push('css')
<style>
    .razorpay-payment-button{
      font-family: 'Montserrat', sans-serif;
      font-size: 13px;
      font-weight: 600;
      line-height: 16px;
      letter-spacing: 0em;
      color: #fff;
      background-color: #E43958;
      width: 100%;
      cursor: pointer;
      outline: none;
      padding: 12px 15px;
      border: none;
      border-radius: 4px;
      text-decoration: none;
   }
   .razorpay-payment-button:hover{
      background: #e6677e;
      border: 1px solid #e6677e;
   }
   .disabled_minus{
      pointer-events: none;
   }
</style>
@endpush
@section('content')

   <div class="team-dash-sec">
      @include('onboarding.setup.layouts.header')
         <div class="add-y-team">
           <div class="row dashboard-row">
               <div class="col-lg-7 col-sm-12 col-md-12">
                   <div class="y-team-header">
                       <h3>Plan, add-ons and billing</h3>
                       <p>Choose your plan, explore additional features, and manage billing details</p>
                   </div>
                           <form class="renew-select" id="plan-boxes">
                               <div class=" renew-boxes">
                                       <label class="renew-single">
                                           <input type="radio" name="renew" value="base_plan" checked>
                                           <div class="r-single-box">
                                               <h5>Existing plan</h5>
                                               @if($userSubscription->no_of_licenses == 0)
                                               <p class="box-bundle">Free plan </p>
                                               @else
                                               <p class="box-bundle">{{$userSubscription->no_of_licenses}} Users </p>
                                               @endif
                                               {{-- <p class="box-bundle">1 SMS Bundle</p> --}}
                                               <div class="expiry-date">
                                                   <p class="renew-date">Expiry: <span>{{\Carbon\Carbon::parse($userSubscription->expiry_date)->format('Y-M-d')}}</span></p>
                                                   <p class="renew-date">Last payment: 
                                                    <span>{{\Carbon\Carbon::parse($userSubscription->updated_at)->format('Y-M-d')}}</span>
                                                </p>
                                               </div>
                                               <p class="renew-price">₹{{ $data['total_amount']}}</p>
                                           </div>
                                       </label>
                                       <label class="renew-single upgrade-single">
                                           <input type="radio" name="renew" value="upgrade">
                                           <div class="r-single-box">
                                               <img class="upgrade-img" src="{{url('onboarding/setup/images/upgrade.svg')}}" alt="GetLead">
                                               <h5 class="customise">Customize your plan</h5>
                                               <p class="renew-date">Customize your plan for ease of use</p>

                                           </div>
                                       </label>
                                       
                               </div>
                               <div class="row upgrade-plans" style="display: none;">
                                   <div class="col-lg-4 col-md-6 col-sm-12">
                                       <label>Number of users</label>
                                       <div class="inp-counter">
                                           <span class="minus disabled_minus">-</span>
                                           <input type="text" value="{{($data['total_users'] != 0)? $data['total_users'] : (($data['agent_count'] != 0)? $data['agent_count'] : 1)}}" class="change_users"/>
                                           <span class="plus">+</span>
                                       </div>
                                   </div>
                                   {{-- @if($data['total_users'] == 0) --}}
                                   <div class="col-lg-4 col-md-6 col-sm-12">
                                       <label>Select period</label>
                                       <select class="change_month">
                                            {{-- <option value="1">1 Month</option> --}}
                                            <option value="3">3 Month</option>
                                            <option value="6">6 Month</option>
                                            <option value="12" selected>12 Month</option>
                                            <option value="16">16 Month</option>
                                            <option value="24">24 Month</option>
                                            <option value="36">36 Month</option>
                                        </select>
                                   </div>
                                   {{-- @else --}}
                                   {{-- <div class="col-lg-4 col-md-6 col-sm-12 d-none">
                                       <label>Select period</label>
                                       <select class="change_month">
                                          <option value="{{($data['total_month'] == 0)? 1 : $data['total_month'] }}">free</option>
                                       </select>
                                    </div>
                                   @endif --}}
                                   <div class="col-lg-4 col-md-6 col-sm-12">
                                        <div class="upgrade-price">
                                            <h4>₹<span class="user_price"></span>.00 <span>/ month</span></h4>
                                            <p>₹7,188 per year</p>
                                        </div>
                                   </div>
                                   {{-- <div class="col-lg-3 col-md-6 col-sm-12">
                                       <div class="currency-convert">
                                           <a class="active" href="#">INR</a>
                                           <a href="#">USD</a>
                                       </div>
                                   </div> --}}
                               </div>
                               <div class="next-btn">
                                   <a href="#">Next</a>
                               </div>
                           </form>
                           
               </div>
               <div class="col-lg-1 col-sm-12 col-md-12"></div>
               <div class="col-lg-4 col-sm-12 col-md-12" id="billing-box">
                   <div class="pricing-summary" >
                    <h4>Pricing summary</h4>
                    <div class="plan-users">
                          <p>Users</p>
                          <div class="users">
                             <p>CRM <span>₹<span class="user_price" > </span> x <span class="user_count" >{{($data['total_users'] != 0)? $data['total_users'] : (($data['agent_count'] != 0)? $data['agent_count'] : 1)}}</span> x <span class="month_count">12</span>  </span></p>
                             <p class="price" data-value="0" id="price_crm">₹0000</p>
                          </div>
                    </div>
                    {{-- <div class="plan-users">
                          <p>Add-ons </p>
                          
                          <div class="users">
                             <p>10 users</p>
                             <p class="price">₹605.00</p>
                          </div>
                          <div class="users">
                             <p>Voice call  </p>
                             <p class="price">₹605.00</p>
                          </div>
                    </div> --}}
                    <div class="plan-users plan-users-2"> 
                          <div class="users ">
                             <p>Promo discount</p>
                             <p class="price" data-value="0" id="promo_discount">₹00.00</p>
                          </div>
                          <div class="users ">
                             <p>GST</p>
                             <p class="price" data-value="0" id="crm_gst">₹0000</p>
                          </div>
                    </div>
                    <div class="plan-users plan-users-2"> 
                          <div class="users users-2">
                             <p></p>
                             <p class="">Total amount: <span id="final_amount" data-value="0">₹0000</span>
                                
                             </p>
                          </div>
                          <div class="users users-2">
                             <p></p>
                             <a href="javascript:void(0)" class="see-all-coupen">See all coupon codes</a>
                          </div>
                          <div class="users users-3">
                             <input type="text" value="" name="coupon-code" id="coupon_code" placeholder="Enter coupon code">
                             <button class="apply-promo">Apply</button>
                          </div>
                          <div class="user-saved">
                             <p class="you_save"></p>
                          </div>
                          
                          <div class="final-price">
                             <p>You have to pay: <span class="payable_amount" data-value="0">₹0000.00</span></p>
                          </div>

                       
                         <div class="price-select">                                
                              <div  class="row">
                                 <div class="col-12 pay-box">
                                    @if($userSubscription->no_of_licenses != 0)
                                       <button id="rzp-button1" class="razorpay-payment-button"  data-id='1' >Proceed to payment</button>
                                    @else
                                       <button id="rzp-button1" class="razorpay-payment-button btn_upgdr" data-id='0'>Please choose upgrade plan</button>    
                                    @endif
                                 </div>
                              </div>
                        </div>
                        
                         {{-- <div class="users users-2 p-3">
                          <a href="javascript:void(0)" class="free-plan text-danger" style="font-size: 15px;">I would like to try it for free</a>
                        </div> --}}
                    </div>
                 </div>
                   <div class="back-btn back-btn-2">
                       <a href="#">Back</a>
                   </div>
               </div>
               
               </div>
         </div>
   </div>

      <!------------------------------------------------ Coupon Modal ------------------------------------------------->
      <div class="modal right fade" id="coupons-modal" tabindex="-1"  data-toggle="modal" role="dialog" aria-labelledby="myModalLabel-task" aria-hidden="true">
        <div class="modal-dialog modal-dialog-full" role="document">
           <div class="modal-content">
              <div class="modal-header" style="padding: 0px">
                 <div class="row" style="width: 100%; margin: 0">
                    <button type="button" class="close" id="closebutton" data-dismiss="modal" aria-label="Close" >
                    <img src="{{url('onboarding/setup/images/modal-close.svg')}}" alt="getlead">
                    </button>
                 </div>
              </div>
              <div class="modal-body">
                 <div class="coupon-inner">
                    <h3><img src="{{url('onboarding/setup/images/coupon.svg')}}" alt="getlead"> Coupon code</h3>
                    <div class="coupens_lists">

                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  <!------------------------------------------ end ------------------------------------------------->

   @push('script')

      <script>
         var url = @JSON(route('add.lead.settings'));
         var url_step3 = @JSON(route('setup-get-mobile-app.list'));
      </script>
         
      <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
      <script>
         name =  @json(Auth::user()->vchr_user_name);
         cid =  @json(Auth::user()->pk_int_user_id);
         mobile =  @json(Auth::user()->vchr_user_mobile);
         email =  @json(Auth::user()->email);
         ret_url = @json(route('setup-confirm-pay'));
         base_url = @json(url('/'));
         total = @json($data['total_amount']);
         page = 'renewal';
         users = @json($userSubscription->no_of_licenses);
         page_redirect = @json(request()->segment(3) ?? '');
         min = @json(($data['total_users'] != 0)? $data['total_users'] : (($data['agent_count'] != 0)? $data['agent_count'] : 1))
      </script>
      <?php
            $key = config('custom.razor_key');
      ?>
      <script type="text/javascript">var key = "<?= $key ?>";</script>

      <script src="{{ url('onboarding/setup/script/billing.js') }}"></script>
      <script>
          loadCurrentPlan();
          $(document).on('click', '.btn_upgdr',function(e){
              e.preventDefault();
              toastr.warning('Please customize your plan before payment is made.');
          })
      </script>
   @endpush
@endsection