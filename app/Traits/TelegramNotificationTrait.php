<?php   

namespace App\Traits;
use Illuminate\Http\Request;

trait TelegramNotificationTrait 
{
    public function send_telegram_notification($data)
    {
        $botToken = "7878063709:AAGxfy4CDDLUCyZjRrmQgRkMLTcsSS8A2aI";
           //'-614845338',
            $data =[
                'chat_id' => '-4966821454', 
                'text'=> "Hey,
        New scratch found, Customer details !!!
        ------------------------------------
        Branch Name => ".$data['branch']."
        Customer Name => ".$data['customer']."
        Mobile No => ".$data['mobile_no'].", 
        Gift Name => ".$data['gift_name'].",
        Campaign Name => ".$data['campaign'].",
        Reference Id => ".$data['reference_id']
                ];
        $response = file_get_contents("https://api.telegram.org/bot$botToken/sendMessage?" .http_build_query($data));
    }
	
}
