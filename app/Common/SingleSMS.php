<?php

namespace App\Common;

use App\BackendModel\DefaultRoute;
use App\BackendModel\DefaultSenderid;
use App\BackendModel\SendSmsHistory;
use App\BackendModel\SmsApiCredentials;
use App\BackendModel\SmsDomain;
use App\BackendModel\Smsroute;
use App\MasterSMSHistory;
use App\SmsPanel;
use App\Subscription\SmsCount;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Common\Common;
use App\BackendModel\EnquiryType;

class SingleSMS
{


    //Set senderid and route for each Gl Tools
    public function storeSenderid($vendorId, $type)
    {
        $exist = DefaultSenderid::where('vendor_id', $vendorId)->where('type', $type)->where('status', DefaultSenderid::ACTIVATE)->first();
        if (!$exist) {
            $sender = new DefaultSenderid();
            $sender->vendor_id = $vendorId;
            $sender->type = $type;
            $sender->senderid = ($type == EnquiryType::GLSCRATCH)? 'GLTCKT' :  DefaultSenderid::SENDERID;
            $sender->save();
        }
    }

    public function storeRoute($vendorId, $type, $routes)
    {
        $exist = DefaultRoute::where('vendor_id', $vendorId)->where('type', $type)->where('status', DefaultRoute::ACTIVATE)->first();
        if (!$exist) {
            $route = new DefaultRoute();
            $route->vendor_id = $vendorId;
            $route->type = $type;
            $route->route = $routes;
            $route->save();
        }
    }

    public static function getSenderid($vendorId, $type)
    {
        $senderid = DefaultSenderid::where('vendor_id', $vendorId)->where('type', $type)->where('status', DefaultSenderid::ACTIVATE)->first();
        if ($senderid) {
            return $senderid->senderid;
        } else {
            if($type == EnquiryType::GLSCRATCH){
                return "GLTCKT";
            }else{
                return "GTLEAD";
            }
        }
    }

    public static function getRoute($vendorId, $type)
    {
        $route = DefaultRoute::where('vendor_id', $vendorId)->where('type', $type)->where('status', DefaultRoute::ACTIVATE)->first();
        if ($route) {
            return $route->route;
        }else{
            $route = DefaultRoute::where('vendor_id', 0)->where('type', $type)->where('status', DefaultRoute::ACTIVATE)->first();
            if ($route) {
                return $route->route;
            }
        }

    }

    /**
     * @param $vendorId
     * @param $routeId
     * @param $smsPanel
     * @return array|int
     * Get SMS Balance
     */
    public function getSMSBalance($vendorId, $routeId, $smsPanel)
    {
        $smsCount = SmsCount::where('vendor_id', $vendorId)->where('route_id', $routeId)->first();
        if ($smsCount) {
            $balance = $smsCount->total_count + $smsCount->credit - $smsCount->used_sms_count;//$smsCount->total_count - $smsCount->used_sms_count;
        } else {
            return $balance = 0;
        }
        if ($balance > 0) {
            $template = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')
                ->where('vendor_id', $vendorId)->first();
            if ($template) {
                $domainId = $template->sms_domain_id;
                $domainDetails = SmsDomain::where('id', $domainId)->first();
                $username = $template->username;
                $api_password = $template->api_password;
                $routeCode = $this->getRouteDetails($routeId)->short_code;
                if ($smsPanel->title == SmsPanel::ALERTBOX) {
                    $smsBalanceUrl = $domainDetails->domain . '/balancecheck.php?' . 'username=' . $username . '&api_password=' . $api_password . '&priority=' . $routeCode;
                    $bal = $this->sendData($smsBalanceUrl);
                    $bal1 = explode(" ", $bal);
                    return $apiBalance = $bal1;
                } else if ($smsPanel->title == SmsPanel::MERABT) {
                    $routeCode= $routeCode=='OTP' ? 'TL' :  $routeCode ;
                    $smsBalanceUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/GET/route/' . $routeCode;
                    $blncData = $this->sendData($smsBalanceUrl);
                    $bal = json_decode($blncData, true);
                    $bal1 = $bal['data']['api'];
                    if ($bal1 == null) {
                        return $apiBalance = 0;
                    }
                    return $apiBalance = $bal1;
                } else if ($smsPanel->title == SmsPanel::TEXT_LOCAL) {
                    $apiKey = urlencode($template->api_password);
                    $data = 'apikey=' . $apiKey;
                    $balance_url = 'https://api.textlocal.in/balance/?' . $data;
                    $bal = json_decode($this->sendData($balance_url), true);

                    if ($bal['status'] == "success") {

                        return $bal['balance']['sms'];
                    } else {
                        return 0;
                    }
                }
            } else {
                return $balance;
            }
        } else {
            return $apiBalance = 0;
        }
    }


    public function updateSMSCount($vendorId, $routeId, $count)
    {
        $smsCount = SmsCount::where('vendor_id', $vendorId)
            ->where('route_id', $routeId)->first();
        if ($smsCount) {
            $usedSms = $smsCount->used_sms_count;
        } else {
            $usedSms = 0;
        }
        $smsCount->used_sms_count = $usedSms + $count;
        $smsCount->save();
        return $smsCount;
    }

    public function getInputSMSCount($inputMessage, $messageType)
    {
        if ($messageType == 0) {
            $length = strlen($inputMessage);
            if (strlen($inputMessage) > 306) {
                $limit = 153;
            } else {
                $limit = 160;
            }
        } elseif ($messageType == 99) {
            $length = strlen($inputMessage);
            $limit = 5;
        } else {
            $length = mb_strlen($inputMessage);
            if (mb_strlen($inputMessage) > 134) {
                $limit = 67;
            } else {
                $limit = 70;
            }
        }
        $messageCharCount = $length;
        $messageCount = $messageCharCount / $limit;
        $count = ceil($messageCount);
        if ($messageType == 99)
            return $count*2;
        return $count;
    }

    public function getRouteDetails($routeId)
    {
        $routeDetails = Smsroute::where('pk_int_sms_route_id', $routeId)->first();
        return $routeDetails;
    }

    /**
     * @param $routeCode
     * @return string
     * Get route id by code
     */
    public function getRouteId($routeCode)
    {
        $routeDetails = Smsroute::where('int_sms_route_code', $routeCode)
            ->select('pk_int_sms_route_id', 'vchr_sms_route', 'short_code', 'priority', 'int_sms_route_code', 'int_sms_route_status')
            ->first();
        if ($routeDetails) {
            return $routeDetails;
        } else {
            return "error";
        }
    }

    public function getSmsUrl($senderId, $mobileNumber, $message, $routeId, $routeCode, $vendorId, $messageType)
    {
        $template = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', $vendorId)->first();
        if ($template) {
            $domainId = $template->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $template->username;
            $api_password = $template->api_password;
            if ($messageType == 0) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&unicode=' . '1';
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1';
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1';
            }
        } else {
            $templates = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', 0)->first();
            $domainId = $templates->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $templates->username;
            $api_password = $templates->api_password;
            if ($messageType == 0) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&unicode=' . '1';
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1';
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1';
            }
        }
    }

    public function getSmsPanel($routeId, $vendorId)
    {
        // Log::info($routeId);
        $template = SmsApiCredentials::where('route_id', $routeId)->where('status', Variables::ACTIVE)->where('vendor_id', $vendorId)->first();
        if ($template) {
            $domainId = $template->sms_domain_id;
        } else {
            $templates = SmsApiCredentials::where('route_id', $routeId)->where('status', Variables::ACTIVE)->where('vendor_id', 0)->first();
            $domainId = $templates->sms_domain_id;
        }
        $domain = SmsDomain::where('id', $domainId)->first();
        return $domain;
    }

    public function getSmsMerabtUrl($senderId, $mobileNumber, $message, $routeId, $routeCode, $vendorId, $messageType)
    {
        $template = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', $vendorId)->first();
        if ($template) {
            $domainId = $template->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $template->username;
            $api_password = $template->api_password;
            if ($messageType == 0) {
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/text/' . $message;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/unicode/1' . '/text/' . $message;
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1';
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1';
            }
        } else {
            $templates = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', 0)->first();
            $domainId = $templates->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $templates->username;
            $api_password = $templates->api_password;
            if ($messageType == 0) {
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/text/' . $message;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/unicode/1' . '/text/' . $message;
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1';
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1';
            }
        }
    }

    public function getSmsTemplateId($routeId, $vendorId)
    {
        $template = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', $vendorId)->first();

        if ($template) {
            return $template->id;
        } else {
            $templates = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', 0)->first();
            return $templates->id;
        }
    }

    public function sendSms($senderId, $mobileNo, $message, $routeCode, $balance, $smsHistoryId, $routeId, $messageType, $vendorId, $smsUrl)
    {
        if ($balance > 0) {
            $sendSMS = $this->sendData($smsUrl);
            return $sendSMS;
        } else {
            $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
            $history->note = 'SMS Balance =' . $balance . '. Please Add Credits';
            $history->save();
        }
    }
    public function sendSmsPost($senderId, $mobileNo, $message, $routeCode, $balance, $smsHistoryId, $routeId, $messageType, $vendorId)
    {
        if ($balance > 0) {
            //$routeDetails = $this->getRouteId($routeCode);
            //if($routeDetails)
            $routeId = 2;
            $sendSMS = $this->sendDataPost($senderId, $mobileNo, $message, $routeId, $routeCode, $vendorId, $messageType, 0);
            return $sendSMS;
        } else {
            $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
            $history->note = 'SMS Balance =' . $balance . '. Please Add Credits';
            $history->save();
        }
    }
    public function sendData($url)
    {
        try{
            $client = new Client([
                'verify' => false, // Set verify to false to allow insecure calls
            ]);
            $response = $client->get($url);
            $result = $response->getBody();
            return $result;
        }catch(\Exception $e){
            \Log::info('Exception message: ' . $e->getMessage());
            return '';
        }
        
    }
    public function sendDataPost($senderId, $mobileNumber, $message, $routeId, $routeCode, $vendorId, $messageType, $shtime)
    {
        $template = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', $vendorId)->first();
        if ($shtime == 0) {
            $shetime = "";
        } else {
            $scheduleTimeExplode = explode('-', $shtime);
            $scheduleDay = $scheduleTimeExplode[0];
            $scheduleMonth = $scheduleTimeExplode[1];
            $scheduleYear = $scheduleTimeExplode[2];
            $schedulHour = $scheduleTimeExplode[3];
            $scheduleMinute = $scheduleTimeExplode[4];
            $shetime = $scheduleYear . '-' . $scheduleMonth . '-' . $scheduleDay . '+' . $schedulHour . ':' . $scheduleMinute;
        }


        if ($template) {
            $domainId = $template->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $template->username;
            $api_password = $template->api_password;
            
        } else {
            $templates = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', 0)->first();
            $domainId = $templates->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $templates->username;
            $api_password = $templates->api_password;
            
        }
		//sms text
        $text = urlencode($message);
 
        $curl = curl_init();
        $post_data=array(
            'mobile' => $mobileNumber,
            'route' => $routeCode,
            'text' => $text,
            'sender' => $senderId);
        if($messageType==1)
            $post_data['unicode'] = '1';
        //Template Matching
            $commonObj = new Common();
            $matching_template = $commonObj->getMatchingDLTTemplate($vendorId,$senderId,$message);
            if($matching_template)
            {
                $post_data['pe_id'] = $matching_template->entity;
                $post_data['pe_template_id'] = $matching_template->template_id;
            }
        //
        // Send the POST request with cURL
        curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => "http://reseller.smschub.com/api/sms/format/json",
        CURLOPT_POST => 1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array('X-Authentication-Key:'.$api_password, 'X-Api-Method:MT'),
        CURLOPT_POSTFIELDS => $post_data));
 
    // Send the request & save response to $response
    $response = curl_exec($curl);
 
    // Close request to clear up some resources
    curl_close($curl);
 
    // Print response
    return $response;
        // $client = new Client();
        // $params = [
        //     'mobile' => $mobileNumber,
        //     'sender' => $senderId,
        //     'text' => urlencode($message),
        //     'route' => $routeCode,
        //     //'unicode' => 1
        // ];
        // if($shetime != "")
        //     $params['shtime'] = $shetime;
        //     $data=[
        //         'form_params'=>$params,
        //         'headers' => [
        //             'X-Authentication-Key' => $api_password,
        //             'X-Api-Method' => 'MT',
        //         ]
        //     ];
        // $response = $client->post($domainDetails->domain . '/api/sms/format/json', $data);
        // return $response->getBody();
                
    }
    public function getResponse($smsHistoryId, $response, $templateId, $routeId, $vendorId, $smsCount)
    {
        // return $response;
        $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $history->api_response = strip_tags($response);
        $history->vchr_status = Variables::SMS_STATUS_SENT;
        $history->save();
        if (strip_tags($response) !== 'Sorry, No valid numbers found!' || strip_tags($response) !== 'Sorry, No valid numbers found!') {
            // Log::info($response);

            /**Get Track Id**/
            $response1 = explode("=", $response);
            $trackId = $response1[1];

            /**Get Message Id**/
            $response2 = explode(" ", $response);
            $response3 = $response2[0];
            $response4 = explode(",", $response3);
            $response5 = $response4[0];
            $response6 = explode("_", $response5);
            $messageId = $response6[1];

            $status_response = $this->checkStatus($messageId, $templateId);
            $messageStatus = explode(":", $status_response);
            $history->vchr_status = trim($messageStatus[1]);
            $history->note = $status_response;
            $history->vchr_alertid = $messageId;
            $history->vchr_trackid = $trackId;
            $history->save();
            $this->updateSMSCount($vendorId, $routeId, $smsCount);
            return $messageId;
        } else {
            $history->vchr_status = Variables::SMS_STATUS_FAIL;
            $history->save();
        }
    }


    public function getMetabtResponse($smsHistoryId, $response, $templateId, $routeId, $vendorId, $smsCount)
    {
        $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $history->api_response = strip_tags($response);
        $history->vchr_status = Variables::SMS_STATUS_SENT;
        $history->save();
        $responseDecode = json_decode($response, true);
        $trackId = isset($responseDecode['data']['campid']) ? $responseDecode['data']['campid'] : '';
        $history->vchr_trackid = $trackId;
        $history->save();
        $this->updateSMSCount($vendorId, $routeId, $smsCount);
        $status_response = $this->checkMetabtStatus($trackId, $templateId);
        $history->note = $status_response;
        $history->save();
        $statusResponse = json_decode($status_response, true);
        $history->vchr_status = isset($statusResponse['data']['report']) ? $statusResponse['data']['report'][0]['dlr'] : 'Sent';
        $history->save();
    }

    public function checkStatus($messageId, $templateId)
    {
        $template = SmsApiCredentials::where('id', $templateId)->first();
        $domainId = $template->sms_domain_id;
        $domainDetails = SmsDomain::where('id', $domainId)->first();
        $username = $template->username;
        $api_password = $template->api_password;
        $url = $domainDetails->domain . '/fetchdlr.php?' . 'username=' . $username . '&msgid=' . $messageId;
        return $status = $this->sendData($url);
    }

    public function checkMetabtStatus($trackId, $templateId)
    {
        $template = SmsApiCredentials::where('id', $templateId)->first();
        $domainId = $template->sms_domain_id;
        $domainDetails = SmsDomain::where('id', $domainId)->first();
        $username = $template->username;
        $api_password = $template->api_password;
        $url = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/DLR/campid/' . $trackId;
        try{
            return $status = $this->sendData($url);
        }catch(\Exception $e){
            \Log::info('merabt status check issue:'.$e->getMessage());
            return '';
        }
        
    }

    public function storeSmsData($vendorId, $templateId, $mobileno, $senderId, $messageType, $route, $message, $messageVia, $routeCode, $routeId, $countMobileno, $smsCount)
    {

        $master_sms_table = new MasterSMSHistory();
        $master_sms_table->vendor_id = $vendorId;
        $master_sms_table->sender_id = $senderId;
        $master_sms_table->message = $message;
        $master_sms_table->sms_route_id = $routeId;
        $master_sms_table->message_count = $smsCount;
        $master_sms_table->credit_count = $countMobileno * $smsCount;
        $master_sms_table->message_type = $messageType;
        $master_sms_table->mobileno_count = $countMobileno;
        $master_sms_table->message_ids = $messageVia;
        $master_sms_table->save();
        $masterSmsId = $master_sms_table->id;

        $sms_history = new SendSmsHistory();
        $sms_history->fk_int_user_id = $vendorId;
        $sms_history->fk_int_template_id = $templateId;
        $sms_history->xml_api_id = $templateId;
        $sms_history->vchr_mobile = $mobileno;
        $sms_history->vchr_sender_id = $senderId;
        $sms_history->vchr_route = $route;
        $sms_history->vchr_messagetype = $messageType;
        $sms_history->message_via = $messageVia;
        $sms_history->text_message = $message;
        $sms_history->vchr_status = Variables::SMS_STATUS_QUEUE;
        $sms_history->int_routecode = $routeCode;
        $sms_history->gl_track_id = $masterSmsId;
        $sms_history->message_count = $smsCount;
        $sms_history->save();
        return $sms_history->pk_int_send_sms_history_id;
    }

    public function updateEnquiry($enquiryId, $smsHistoryId)
    {
        $sms_history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $sms_history->enquiry_followup_id = $enquiryId;
        $sms_history->save();
    }

//-----------------------Developer Api----------------------------------

    public function storeSmsDataDeveloperApi($vendorId, $templateId, $mobileno, $senderId, $messageType, $route, $message, $messageVia, $routeCode, $routeId, $countMobileno, $smsCount, $masterSmsId, $responses, $shtime, $isVoice=false)
    {
        $sms_history = new SendSmsHistory();
        $sms_history->fk_int_user_id = $vendorId;
        $sms_history->fk_int_template_id = $templateId;
        $sms_history->xml_api_id = $templateId;
        $sms_history->vchr_mobile = $mobileno;
        $sms_history->vchr_sender_id = $senderId;
        $sms_history->vchr_route = $route;
        $sms_history->vchr_messagetype = $messageType;
        $sms_history->message_via = $messageVia;
        $sms_history->text_message = $message;
        $sms_history->vchr_status = Variables::SMS_STATUS_QUEUE;
        $sms_history->int_routecode = $routeCode;
        $sms_history->gl_track_id = $masterSmsId;
        $sms_history->message_count = $isVoice ? $smsCount/2 : $smsCount;
        $sms_history->api_response = $responses;
        $sms_history->vchr_schedule = $shtime;
        $sms_history->save();
        return $sms_history->pk_int_send_sms_history_id;
    }

    public function storeMasterSmsDataDeveloperApi($vendorId, $templateId, $mobileno, $senderId, $messageType, $route, $message, $messageVia, $routeCode, $routeId, $countMobileno, $smsCount, $isVoice=false)
    {
        $master_sms_table = new MasterSMSHistory();
        $master_sms_table->vendor_id = $vendorId;
        $master_sms_table->sender_id = $senderId;
        $master_sms_table->message = $message;
        $master_sms_table->sms_route_id = $routeId;
        $master_sms_table->message_count = $isVoice ? $smsCount/2 : $smsCount;
        $master_sms_table->credit_count = $countMobileno * $smsCount;
        $master_sms_table->message_type = $messageType;
        $master_sms_table->mobileno_count = $countMobileno;
        $master_sms_table->message_ids = $messageVia;
        $master_sms_table->save();
        return $masterSmsId = $master_sms_table->id;
    }

    public function getResponseDeveloperApi($smsHistoryId, $response, $templateId, $routeId, $vendorId, $smsCount, $trackId, $messageId)
    {
        $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $history->api_response = strip_tags($response);
        $history->vchr_status = Variables::SMS_STATUS_SENT;
        $history->save();
        if (strip_tags($response) !== 'Sorry, No valid numbers found!' || 'Sorry, No valid numbers found!  Trackid=Sorry, No valid numbers found!') {
            $status_response = $this->checkStatus($messageId, $templateId);
            $messageStatus = explode(":", $status_response);
            $history->vchr_status = trim($messageStatus[1]);
            $history->note = $status_response;
            $history->vchr_alertid = $messageId;
            $history->vchr_trackid = $trackId;
            $history->save();
            $this->updateSMSCount($vendorId, $routeId, $smsCount);
            return $messageId;
        } else {
            $history->vchr_status = Variables::SMS_STATUS_FAIL;
            $history->save();
        }

    }

    public function getResponseDeveloperApiOtp($smsHistoryId, $response, $templateId, $routeId, $vendorId, $smsCount, $trackId, $messageId)
    {
        $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $history->api_response = strip_tags($response);
        $history->vchr_status = Variables::SMS_STATUS_SENT;
        $history->save();
        if (strip_tags($response) !== 'Sorry, No valid numbers found!' || 'Sorry, No valid numbers found!  Trackid=Sorry, No valid numbers found!') {
            $history->vchr_alertid = $messageId;
            $history->vchr_trackid = $trackId;
            $history->vchr_status = Variables::SMS_STATUS_SENT;
            $history->save();
            $this->updateSMSCount($vendorId, $routeId, $smsCount);
            return $messageId;
        } else {
            $history->vchr_status = Variables::SMS_STATUS_FAIL;
            $history->save();
        }
    }


    public function getSmsUrlDeveloperApi($senderId, $mobileNumber, $message, $routeId, $routeCode, $vendorId, $messageType, $shtime, $result_type)
    {
        $template = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', $vendorId)->first();
        if ($shtime == 0) {
            $shetime = "";
        } else {
            $shetime = "&shtime=" . $shtime;
        }
        if ($result_type == 2) {
            $result_type = "&result_type=2";
        } else {
            $result_type = "";
        }
        if ($template) {
            $domainId = $template->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $template->username;
            $api_password = $template->api_password;
            if ($messageType == 0) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . $shetime . $result_type;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&unicode=' . '1' . $shetime . $result_type;
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1' . $shetime . $result_type;
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1' . $shetime . $result_type;
            }
        } else {
            $templates = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', 0)->first();
            $domainId = $templates->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $templates->username;
            $api_password = $templates->api_password;
            if ($messageType == 0) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . $shetime . $result_type;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&unicode=' . '1' . $shetime . $result_type;
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1' . $shetime . $result_type;
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1' . $shetime . $result_type;
            }

        }
    }

    public function updateCreditCountDeveloperApi($id, $count)
    {
        $sms_history = MasterSMSHistory::find($id);
        $sms_history->credit_count = $count;
        $sms_history->save();
    }

    public function getMasterSmsDetails($id)
    {
        $sms_history = MasterSMSHistory::find($id);
        return $sms_history;
    }

    public function getSmsHistoryDetails($gltrackid)
    {
        $sms_history = SendSmsHistory::where('gl_track_id', $gltrackid)->get();
        return $sms_history;
    }

    public function checkExistSmsHistoryDetails($gltrackid)
    {
        $sms_history = SendSmsHistory::where('gl_track_id', $gltrackid)->first();
        return $sms_history;
    }

    public function getSmsMerabtDeveloperApiUrl($senderId, $mobileNumber, $message, $routeId, $routeCode, $vendorId, $messageType, $shtime)
    {
        $template = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', $vendorId)->first();
        if ($shtime == 0) {
            $shetime = "";
        } else {
            $scheduleTimeExplode = explode('-', $shtime);
            $scheduleDay = $scheduleTimeExplode[0];
            $scheduleMonth = $scheduleTimeExplode[1];
            $scheduleYear = $scheduleTimeExplode[2];
            $schedulHour = $scheduleTimeExplode[3];
            $scheduleMinute = $scheduleTimeExplode[4];
            $shetime = "/shtime=" . $scheduleYear . '-' . $scheduleMonth . '-' . $scheduleDay . '+' . $schedulHour . ':' . $scheduleMinute;
        }

        //$message = rawurlencode($message);
        if ($template) {
            $domainId = $template->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $template->username;
            $api_password = $template->api_password;
            if ($messageType == 0) {
                //return $smsUrl = $domainDetails->domain . '/api/sms?format=json&key=' . $api_password . '&method=MT&unicode=1&mobile=' . $mobileNumber . '&sender=' . $senderId . '&text=' . $message . '&route=' . $routeCode.($shetime != "" ? '&'.substr($shetime,1) : '');
                
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/text/' . $message . $shetime;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/unicode/1' . '/text/' . $message . $shetime;
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1';
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1';
            }
        } else {
            $templates = SmsApiCredentials::where('route_id', $routeId)->where('status', '1')->where('vendor_id', 0)->first();
            $domainId = $templates->sms_domain_id;
            $domainDetails = SmsDomain::where('id', $domainId)->first();
            $username = $templates->username;
            $api_password = $templates->api_password;
            if ($messageType == 0) {
                //if($shetime=='')
                //return $smsUrl = $domainDetails->domain . '/api/sms?format=json&key=' . $api_password . '&method=MT&mobile=' . $mobileNumber . '&sender=' . $senderId . '&text=' . $message . '&route=' . $routeCode.($shetime != "" ? '&'.substr($shetime,1) : '');
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/text/' . $message . $shetime;
            } else if ($messageType == 1) {
                return $smsUrl = $domainDetails->domain . '/api/sms/format/json/key/' . $api_password . '/method/MT/mobile/' . $mobileNumber . '/sender/' . $senderId . '/route/' . $routeCode . '/unicode/1' . '/text/' . $message . $shetime;
            } else if ($messageType == 2) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&flash=' . '1';
            } else if ($messageType == 3) {
                return $smsUrl = $domainDetails->domain . '/pushsms.php?' . 'username=' . $username . '&api_password=' . $api_password . '&sender=' . $senderId . '&to=' . $mobileNumber . '&message=' . $message . '&priority=' . $routeCode . '&picture=' . '1';
            }
        }
    }


    public function getMetabtDeveloperApiResponse($smsHistoryId, $response, $templateId, $routeId, $vendorId, $smsCount, $i)
    {
        $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $history->api_response = strip_tags($response);
        $history->vchr_status = Variables::SMS_STATUS_SENT;
        $history->save();
        $responseDecode = json_decode($response, true);
        $trackId = isset($responseDecode['data']['campid']) ? $responseDecode['data']['campid'] : null;
        $history->vchr_trackid = $trackId;
        $history->save();
        $this->updateSMSCount($vendorId, $routeId, $smsCount);
        $status_response = $this->checkMetabtStatus($trackId, $templateId);
        $history->note = $status_response;
        $history->save();
        $statusResponse = json_decode($status_response, true);
        $history->vchr_status = isset($statusResponse['data']['report']) ? $statusResponse['data']['report'][$i]['dlr'] : 'Sent';
        $history->save();
    }
    public function getAwsSNSDeveloperApiResponse($smsHistoryId, $response, $templateId, $routeId, $vendorId, $smsCount, $i)
    {
        $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $history->api_response = strip_tags($response);
        $history->vchr_status = Variables::SMS_STATUS_DELIVERED;
        $history->save();
        $responseDecode = json_decode($response, true);
        $trackId = isset($responseDecode['msgid']) ? $responseDecode['msgid'] : null;
        $history->vchr_trackid = $trackId;
        $history->save();
        $this->updateSMSCount($vendorId, $routeId, $smsCount);
        // $status_response = $this->checkMetabtStatus($trackId, $templateId);
        // $history->note = $status_response;
        // $history->save();
        // $statusResponse = json_decode($status_response, true);
        // $history->vchr_status = isset($statusResponse['data']['report']) ? $statusResponse['data']['report'][$i]['dlr'] : 'Sent';
        // $history->save();
    }

    /*---------------------------------------------------------------------------------------*/
    /* SMS : TEXT LOCAL */
    /*---------------------------------------------------------------------------------------*/

    /**
     * Get SMS Panel Balance
     * @param $vendorId
     * @param $routeId
     * @param $smsPanel
     * @param $priority
     * @return array|int
     */
    public function getSmsPanelBalance($vendorId, $routeId, $smsPanel, $priority)
    {
        $template = SmsApiCredentials::where('route_id', $routeId)
            ->where('status', Variables::ACTIVE)
            ->where('vendor_id', $vendorId)
            ->first();
        if (!$template) {
            $template = SmsApiCredentials::where('route_id', $routeId)
                ->where('status', Variables::ACTIVE)
                ->first();
        }
        if ($template) {
            $username = $template->username;
            $api_password = $template->api_password;
            if ($smsPanel->title == SmsPanel::ALERTBOX) {
                $smsBalanceUrl = $smsPanel->domain . '/balancecheck.php?' . 'username=' . $username . '&api_password=' . $api_password . '&priority=' . $priority;
                $bal = $this->sendData($smsBalanceUrl);
                $bal1 = explode(" ", $bal);
                return $apiBalance = $bal1;
            } else if ($smsPanel->title === SmsPanel::TEXT_LOCAL) {

                $data = 'apikey=' . urlencode($template->api_password);
                $balance_url = 'https://api.textlocal.in/balance/?' . $data;
                $bal = json_decode($this->sendData($balance_url), true);
                if ($bal['status'] == "success") {
                    return $bal['balance']['sms'];
                } else {

                    return 0;
                }
            }
        } else {

            return 0;
        }
    }

    /**
     * @param $vendorId
     * @param $smsRouteId
     * @param $sms_domain_id
     * @return mixed
     */
    public function getTextLocalCredentials($vendorId, $smsRouteId, $sms_domain_id)
    {
        $credentials = SmsApiCredentials::where('route_id', $smsRouteId)->where('status', '1')
            ->where('vendor_id', $vendorId)
            ->where('sms_domain_id', $sms_domain_id)
            ->first();
        if ($credentials) {
            return $credentials;
        } else {
            $credentials = SmsApiCredentials::where('route_id', $smsRouteId)->where('status', '1')
                ->where('sms_domain_id', $sms_domain_id)
                ->first();
            return $credentials;
        }
    }

    /**
     * Get Text local sms Url
     * @param $senderId
     * @param $inputMessage
     * @param $apiKey
     * @param $numbers
     * @return string
     *
     */
    public function getTextLocalSmsUrl($senderId, $inputMessage, $apiKey, $numbers)
    {
        // Message details
        $content = $inputMessage;
        //$inputMessage = rawurlencode($content);
        $apiKey = urlencode($apiKey);
        $numbers = urlencode($numbers);
        $senderId = urlencode($senderId);
        $smsText = rawurlencode($inputMessage);
        $data = 'apikey=' . $apiKey . '&numbers=' . $numbers . "&sender=" . $senderId . "&message=" . $smsText;
        return $data;
    }

    /**
     * Update: Text Local Response
     * @param $smsHistoryId
     * @param $response
     * @param $status
     * @param $routeId
     */
    public function updateTextLocalResponse($smsHistoryId, $response, $status, $routeId)
    {
        $history = SendSmsHistory::where('pk_int_send_sms_history_id', $smsHistoryId)->first();
        $history->api_response = $response;
        $history->vchr_status = $status;
        $responseJson = json_decode($response, true);
        if ($responseJson['status'] == "success") {
            $this->updateSMSCount($history->fk_int_user_id, $routeId, $responseJson['cost']);
            $history->vchr_trackid = $responseJson['batch_id'];
            $history->vchr_alertid = $responseJson['messages']['0']['id'];
        }
        $history->created_by = $history->fk_int_user_id;
        $history->save();
    }

}