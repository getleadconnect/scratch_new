<?php
namespace App\Traits;

use App\BillingSubscription;
use App\Subscription\Plan;
use App\Subscription\PlanService;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;

trait BillingTrait
{

    protected static $BASE_URL = 'https://billing.getleadcrm.com';

    /**
     * @return \Illuminate\Http\JsonResponse
     * New user data insertion to billing
     * @author AJAY
     */

    public static function userPushDataToBilling($plan_id,$vendor_id)
    {
        $data = self::getPlansAndSubscriptions($plan_id,$vendor_id);

        $client = new Client();
        $url = "https://billing.getleadcrm.com/api/save-from-crm";

        $params = $data;

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = $client->request('POST', $url, [
            'json' => $params,
            'headers' => $headers,
            'verify'  => false,
        ]);

        $responseBody = json_decode($response->getBody(),true);
        \Log::info($responseBody);
        return $responseBody;
    }

    public static function getPlansAndSubscriptions($plan_id,$vendor_id){
        $user = User::select('vchr_user_name','email','countrycode','mobile','pk_int_user_id')->find($vendor_id);
        // $plan = Plan::find($plan_id);
        // $plan_services = $plan ? PlanService::where('plan_id', $plan_id)->pluck('service')->toArray() : [];
        // $totalAmount = $plan ? round($plan->tax_amount + $plan->net_amount) : 0;

        $data = [];
        $data['user_name']= $user->vchr_user_name;
        $data['email']= $user->email;
        $data['countrycode']= $user->countrycode;
        $data['mobile']= $user->mobile;
        $data['pk_int_user_id']= $vendor_id;
        // $data['date_to']= ($plan)? Carbon::today()->addDays($plan->duration)->toDateString() : null;
        // $data['service_plan_id'] = $plan->name ?? null; 
        // $data['services']=  $plan_services;
        $data['start_date']= Carbon::today()->toDateString();
        // $data['end_date']= ($plan)? Carbon::today()->addDays($plan->duration)->toDateString() : null;
        // $data['duration_in_days']= $plan->duration ?? 0;
        // $data['quantity']= '1';
        // $data['discount_mode']= '1';
        // $data['discount_value']= '0';
        // $data['amount']= $totalAmount;

        return $data;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * New user data insertion to billing
     * @author AJAY
     */

     public static function userSubscriptionToBilling($vendor_id,$days=null)
     {
         $data = [];
         $data['client_id']= $vendor_id;
         $data['days']= $days;
 
         $client = new Client();
         $url = "https://billing.getleadcrm.com/api/subscribe-to-trial-plan-from-crm";
 
         $params = $data;

         $headers = [
             'Content-Type' => 'application/json'
         ];
 
         $response = $client->request('POST', $url, [
             'json' => $params,
             'headers' => $headers,
             'verify'  => false,
         ]);
 
         $responseBody = json_decode($response->getBody(),true);

         return $responseBody;
     }

      /**
     * @return \Illuminate\Http\JsonResponse
     * Fetch promococde data from billing
     * @author AJAY
     */

     public static function fetchPromocodeData($month)
     {
         $data = [];
         $data['months']= $month;
 
         $client = new Client();
         $url = "https://billing.getleadcrm.com/api/list-promo-codes";
 
         $params = $data;
 
         $headers = [
             'Content-Type' => 'application/json'
         ];
 
         $response = $client->request('POST', $url, [
             'json' => $params,
             'headers' => $headers,
             'verify'  => false,
         ]);
 
         $responseBody = json_decode($response->getBody(),true);
         
         return $responseBody;
     }

     /**
     * @return \Illuminate\Http\JsonResponse
     * submit payment data to billing
     * @author AJAY
     */

    public static function submitPaymentDataToBilling($data)
    {
        $client = new Client();
        $url = "https://billing.getleadcrm.com/api/payment-details";

        $params = $data;
        \Log::info($data);
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = $client->request('POST', $url, [
            'json' => $params,
            'headers' => $headers,
            'verify'  => false,
        ]);

        $responseBody = json_decode($response->getBody(),true);
        \Log::info($responseBody);
        return $responseBody;
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * Sync from billing
     * @author AJAY
     */

     public static function syncFromBilling()
     {
         $client = new Client();
         $url = "https://billing.getleadcrm.com/api/sync-from-billing";
 
         $params = [];
 
         $headers = [
             'Content-Type' => 'application/json'
         ];
 
         $response = $client->request('POST', $url, [
             'json' => $params,
             'headers' => $headers,
             'verify'  => false,
         ]);
 
         $responseBody = json_decode($response->getBody(),true);

         foreach($responseBody['data'] as $item){
            if($item['status'] == 1){
                if(isset($item['billing_subscriptions'][0])){
                        foreach ($item['billing_subscriptions'] as $key => $bill) {
                            $subscription = new BillingSubscription();
                            $subscription->fk_int_user_id = $item['id'];
                            $subscription->vendor_id = $item['vendor_id'];
                            $subscription->no_of_licenses = $bill['no_of_licenses'];
                            $subscription->plan_type = $bill['plan_type'] ?? 1;
                            $subscription->services = json_encode(['CRM']);
                            $subscription->expiry_date = $bill['end_date'];
                            $subscription->start_date = $bill['start_date'];
                            $subscription->billing_id = $bill['id'];
                            $subscription->amount = $bill['amount'];
                            $subscription->promo_code_id = $bill['promo_code_id'];
                            $subscription->promo_code_value = $bill['promo_code_value'];
                            $subscription->additional_discount = $bill['additional_discount'];
                            $subscription->currency = $bill['currency'];
                            $subscription->status = 1;
                            $subscription->save();
                        }
                    }
                }
            }
            return 'success';
     }

      /**
     * @return \Illuminate\Http\JsonResponse
     * Send verification mail
     * @author AJAY
     */

     public static function sendVerificationMail($otp,$email,$user_name)
     {
         $data = [];
         $data['user_name']= $user_name;
         $data['email_id']= $email;
         $data['otp']= $otp;
 
         $client = new Client();
         $url = "https://billing.getleadcrm.com/api/send-verification-mail";
 
         $params = $data;
 
         $headers = [
             'Content-Type' => 'application/json'
         ];
 
         $response = $client->request('POST', $url, [
             'json' => $params,
             'headers' => $headers,
             'verify'  => false,
         ]);
 
         $responseBody = json_decode($response->getBody(),true);
         
         return $responseBody;
     }

     /**
     * @return \Illuminate\Http\JsonResponse
     * Get user subscription
     * @author AJAY
     */

     public static function getUserSubscription($vendor_id)
     {
         $data = [];
         $data['vendor_id']= $vendor_id;
 
         $client = new Client();
         $url = "https://billing.getleadcrm.com/api/get-user-subscription";
 
         $params = $data;
 
         $headers = [
             'Content-Type' => 'application/json'
         ];
 
         $response = $client->request('POST', $url, [
             'json' => $params,
             'headers' => $headers,
             'verify'  => false,
         ]);
 
         $responseBody = json_decode($response->getBody(),true);
         
         return $responseBody;
     }

     /**
     * @return \Illuminate\Http\JsonResponse
     * Update billing user information
     * @author AJAY
     */

     public static function updateUserInformation($user){
         $data = [];
         $data['name']= $user->vchr_user_name;
         $data['email']= $user->email;
         $data['mobile']= $user->vchr_user_mobile;
         $data['vendor_id']= $user->pk_int_user_id;
         $client = new Client();
         $url = self::$BASE_URL."/api/update-user-info";
 
         $params = $data;
 
         $headers = [
             'Content-Type' => 'application/json'
         ];
 
         $response = $client->request('POST', $url, [
             'json' => $params,
             'headers' => $headers,
             'verify'  => false,
         ]);
 
         $responseBody = json_decode($response->getBody(),true);
         
         return $responseBody;
     }

     public static function freeTrial(){
        try{
            $client = self::userSubscriptionToBilling(User::getVendorId());
            $subscription = new BillingSubscription();
            $subscription->fk_int_user_id = $client['client_id'] ?? 0;
            $subscription->vendor_id = User::getVendorId();
            $subscription->no_of_licenses = 0;
            $subscription->plan_type = 1;
            $subscription->services = json_encode(['CRM']);
            $subscription->expiry_date = now()->addDays(14)->format("Y-m-d");
            $subscription->billing_id = $client['billing_id'] ?? 0;
            $subscription->amount = 0;
            $subscription->promo_code_id = null;
            $subscription->promo_code_value = '';
            $subscription->additional_discount = '';
            $subscription->currency = 'INR';
            $subscription->status = 1;
            $subscription->save();

            return response()->json(['status' => 1 , 'message' => 'Plan Successfully taken'],200);
        }catch(\Exception $e){
            \Log::info($e->getMessage());
            return response()->json(['status' => 0 , 'message' => 'Invalid'],403);
        }
     }
}
