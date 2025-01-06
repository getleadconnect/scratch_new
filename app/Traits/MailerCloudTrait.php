<?php
namespace App\Traits;

use App\AutomationRule;
use App\BackendModel\Enquiry;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;

trait MailerCloudTrait
{
    public function addToMailerContact($token,$dataSet,$contact_id,$status)
    {
        $client = new Client();
        $url = "https://cloudapi.mailercloud.com/v1/contacts";
        $params = [
            'name' => $this->fetchContactName($dataSet['name'],$dataSet['vchr_customer_email']),
            'email' => $dataSet['vchr_customer_email'],
            'phone' => $dataSet['phone'],
            'lead_source' => $dataSet['lead_source'],
            'list_id' => $contact_id,
            "custom_fields" => [
                $status => $dataSet['lead_status']
            ]
        ];

        $headers = [
            'Authorization' => $token
        ]; 
      
        try {
            $response = $client->request('POST', $url, [
                'json' => $params,
                'headers' => $headers,
                'verify'  => false,
            ]);

        $responseBody = json_decode($response->getBody());
        $this->updateEmailSync($dataSet['pk_int_enquiry_id']);
        return true;
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            \Log::info('Mailer cloud failed: Email-'.$dataSet['vchr_customer_email']);
        }

        return false;
    }

    public function createCustomProperty($token)
    {
        $params = [
            [
                'description' => 'CRM Lead Source',
                'name' => 'Lead Source',
                'type' => 'text'
            ],
            [
                'description' => 'CRM Lead Purpose',
                'name' => 'Lead Purpose',
                'type' => 'text'
            ],
            [
                'description' => 'CRM Lead Status',
                'name' => 'Lead Status',
                'type' => 'text'
            ]
        ];

        foreach ([0,1,2] as $value) {
            try {
                $client = new Client();
                $url = "https://cloudapi.mailercloud.com/v1/contact/property";
                $headers = [
                    'Authorization' => $token,
                    'Content-Type' => 'application/json'
                ];

                $client->request('POST', $url, [
                    'json' => $params[$value],
                    'headers' => $headers,
                    'verify'  => false,
                ]);

            } catch (\Exception $e) {
                \Log::info('Mailer cloud failed'.$e->getMessage());
            }
        }
            
        return true;    
    } 

    public function createContact($token)
    {
        $client = new Client();
        $url = "https://cloudapi.mailercloud.com/v1/list";
        $headers = [
            'Authorization' => $token
        ];

        $params = [
            "list_type"=> 1,
            "name" => "Getlead Crm"
        ];

        try {
            $response = $client->request('POST', $url, [
                'json' => $params,
                'headers' => $headers,
                'verify'  => false,
            ]);

        $responseBody = json_decode($response->getBody());
        } catch (\Exception $e) {
            \Log::info('Mailer cloud failed'.$e->getMessage());
            \Log::info($responseBody);
        }
            
        return true;    
    } 
    
    public function fetchContactList($token)
    {
        $responseBody = [];
        $client = new Client();
        $url = "https://cloudapi.mailercloud.com/v1/lists/search";
        $headers = [
            'Authorization' => $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $params = [
            "limit"=> 100,
            "list_type"=> 1,
            "page"=> 1,
            "search_name"=> "",
            "sort_field"=> "",
            "sort_order"=> ""
        ];

        try {
            $response = $client->request('POST', $url, [
                'json' => $params,
                'headers' => $headers,
                'verify'  => false,
            ]);

        $responseBody = json_decode($response->getBody());
        } catch (\Exception $e) {
            \Log::info('Mailer cloud failed'.$e->getMessage());
        }
            
        return $responseBody;    
    }

    public function searchProperty($token)
    {
        $responseBody = [];
        $client = new Client();
        $url = "https://cloudapi.mailercloud.com/v1/contact/property/search";
        $headers = [
            'Authorization' => $token,
            'Content-Type' => 'application/json'
        ];

        $params = [
            "limit"=> 10,
            "page"=> 1,
            "search"=> "lead status",
            "type"=> "",
        ];

        try {
            $response = $client->request('POST', $url, [
                'json' => $params,
                'headers' => $headers,
                'verify'  => false,
            ]);

        $responseBody = json_decode($response->getBody());
        } catch (\Exception $e) {
            \Log::info('Mailer cloud failed'.$e->getMessage());
        }
            
        return $responseBody;    
    }

    public function updateEmailSync($enquiry_id){
        Enquiry::select('pk_int_enquiry_id','email_sync')->where('pk_int_enquiry_id',$enquiry_id)->update(['email_sync' => 1]);
    }

    public static function fetchContactName($name,$email){
        $response = '';
        if($name != ''){
            if(strlen($name) > 25){
                $parts = explode(' ', $name);
                $firstName = $parts[0];
                $response = $firstName;
            }else{
                $response = $name;
            }
        }else{
            $parts = explode('@', $email);
            $emailName = $parts[0];
            $response = $emailName;
        }

        return $response;
    }
}
