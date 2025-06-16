<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendSms($to, $message)
    {
        try {
            return $this->twilio->messages->create($to, [
                'from' => config('services.twilio.from'), // usando número desde el .env
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error("Error enviando SMS: " . $e->getMessage());
            return false;
        }
    }
}
