<?php
namespace App\Traits;

use GuzzleHttp\Client;

trait MessagingTrait
{

    /**
     * @return \Illuminate\Http\JsonResponse
     * push Whatsapp Message to openai
     * @author AJAY
     */

    public static function pushWhatsappMessageGPT($data)
    {
        $client = new Client();

        $url = "https://unnikuttan.getleadcrm.com/api/reply-to-this";

        $params = [
            'question' => ($data['messages'][0]['type'] == 'text')? $data['messages'][0]['text']['body'] : '',
            'mobile' => $data['contacts'][0]['wa_id']
        ];

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
     * push Whatsapp Message to openai
     * @author AJAY
     */

     public static function pushWhatsappMessageGlChat($data)
     {
         $client = new Client();
 
         $url = "https://gl-ai.getleadcrm.com/api/gl-support-chat";
 
         $params = [
             'message' => ($data['messages'][0]['type'] == 'text')? $data['messages'][0]['text']['body'] : '',
             'mobile' => $data['contacts'][0]['wa_id']
         ];
 
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
     * Send telinfy whatasapp message
     * @author AJAY
     */
    public static function sendTelinfyWhatsappMessage($data)
     {
        if(substr(request()->mobile, 0, 1) != "+"){
            request()->merge([
                'mobile' => '+' . request()->mobile
            ]);
        }else{
            request()->merge([
                'mobile' => request()->mobile
            ]);
        }

        $client = new Client();
        $url = "https://api.telinfy.net/gaca/whatsapp/message/direct";

        $params = [
            'to' => request()->mobile,
            'type' => request()->type,
            'text' => [
                'previewUrl' => true,
                'body' => request()->message,
            ],
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Api-Key' => 'bd4c868e-9672-443b-86cf-ad1e14e9405b'
        ];

        $response = $client->request('POST', $url, [
            'json' => $params,
            'headers' => $headers,
            'verify'  => false,
        ]);

        $responseBody = json_decode($response->getBody(),true);
        
        return $responseBody;
     }

}
