<?php

namespace App\Services\SmsService;

use Illuminate\Support\Facades\Http;

class Kavenegar implements SmsServiceInterface
{
    public function __construct(private string $apiKey)
    {
    }

    public function sendSms(string $recipient, string $message): bool
    {
        $url = "https://api.kavenegar.com/v1/{$this->apiKey}/sms/send.json";
        $response = Http::get($url, [
            'receptor' => $recipient,
            'message' => $message,
        ]);

        $response->throw();

        return true;
    }
}
