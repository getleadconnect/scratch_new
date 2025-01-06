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
                     <p>A CRM billing that allows users to securely enter and manage their payment information for services or products provided by the CRM.</p>
                  </div>
                  <form class="renew-select" id="plan-boxes">
                        <div class="row upgrade-plans">
                           <div class="col-lg-4 col-md-6 col-sm-12">
                                 <label>Number of users</label>
                                 <div class="inp-counter">
                                    <span class="minus">-</span>
                                    <input type="text" value="1" class="change_users"/>
                                    <span class="plus">+</span>
                                 </div>
                           </div>
                           <div class="col-lg-4 col-md-6 col-sm-12 ">
                                 <label>Select period</label>
                                 {{-- <div class="inp-counter">
                                    <span class="minus_month">-</span>
                                    <input type="text" value="1" class="change_month"/>
                                    <span class="plus_month">+</span>
                                 </div> --}}
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
                           <div class="col-lg-4 col-md-6 col-sm-12">
                                 <div class="upgrade-price">
                                    <h4>₹599.00 <span>/ month</span></h4>
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
                              <p>CRM <span>₹599 x <span class="user_count" >1</span> x <span class="month_count">12</span>  </span></p>
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
                                      <button id="rzp-button1" class="razorpay-payment-button">Proceed to payment</button>
                                  </div>
                              </div>
                          </div>
                          <div class="users users-2 p-3">
                           <a href="javascript:void(0)" class="free-plan text-danger" style="font-size: 15px;">I would like to try it for free</a>
                         </div>
                     </div>
                  </div>
                  <div class="back-btn back-btn-2">
                     <a href="#">Back</a>
                  </div>
               </div>
         </div>
      </div>
    
      <div class="bottom-btns bottom-btns-v2">
         <div class="back-btn">
            {{-- <a href="#">Skip</a> --}}
         </div>
         <div class="bottom-btns bottom-btns-v2" style="padding-top:0;">
            <div class="back-btn">
               <a href="{{route('setup-get-mobile-app.list')}}">Back</a>
            </div>
            <div class="next-btn">
               {{-- <a href="javascript:void(0)" class="submit-billing">
                  Next
               </a> --}}
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
                     {{-- <div class="coupon-box active">
                        <div class="title-btn">
                           <p>GL015</p>
                           <a href="#">APPLY </a>
                        </div>
                        <p class="coupon-title">To  install the Getlead app, either</p>
                        <p class="coupon-cont">To  install the Getlead app, either scan the QR code and download</p>
                     </div> --}}
                     {{-- <div class="coupon-box">
                        <div class="title-btn">
                           <p>GL015</p>
                           <a href="#">APPLY </a>
                        </div>
                        <p class="coupon-title">To  install the Getlead app, either</p>
                        <p class="coupon-cont">To  install the Getlead app, either scan the QR code and download</p>
                     </div> --}}
                     {{-- <div class="coupon-box">
                        <div class="title-btn">
                           <p>GL015</p>
                           <a href="#">APPLY </a>
                        </div>
                        <p class="coupon-title">To  install the Getlead app, either</p>
                        <p class="coupon-cont">To  install the Getlead app, either scan the QR code and download</p>
                     </div> --}}

                     
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
         page = 'pricing';
         total = 599;
         page_redirect = 'pricing';
   
      </script>
      <?php
            $key = config('custom.razor_key');
      ?>
      <script type="text/javascript">var key = "<?= $key ?>";</script>

      <script src="{{url('onboarding/setup/script/billing.js')}}"></script>

   @endpush
@endsection