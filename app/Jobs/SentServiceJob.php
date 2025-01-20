<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SentServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url,$postData,$headers;
    /**
     * Create a new job instance.
     */
    public function __construct($url,$postData,$headers)
    {
        $this->url = $url;
        $this->postData = $postData;
        $this->headers = $headers;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $client = new Client();
        try {
            $response = $client->request('POST', $this->url, [
                'json' => $this->postData,
                'headers' => $this->headers,
            ]);
            
			$result=(string) $response->getBody();
			return $result;
			
        } catch (\Exception $e) {
            Log::info('Sent service job failed: ' . $e->getMessage());
            return $e->getMessage();
        }
    }
}
