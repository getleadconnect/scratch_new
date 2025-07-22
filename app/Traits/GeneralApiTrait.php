<?php
namespace App\Traits;

trait GeneralApiTrait
{

    /**
     * @return \Illuminate\Http\JsonResponse
     * @author AJAY
     */

    public static function getTallyPurchaseAmount($request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.coremicron.com/zam/mobile-api/customer-balance.php?device_id='.$request->device_id.'&mobile='.$request->mobile_no,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST'
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        return $response;
    }
}
