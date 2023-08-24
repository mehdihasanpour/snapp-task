<?php

namespace App\Services\SmsService;

class Ghasedak implements SmsServiceInterface
{
    public function sendSms(string $recipient, string $message): bool
    {
        // Implement the logic to send an SMS using MySmsProvider API
        // Example code here
        return true; // Return true on successful sending
    }
}
