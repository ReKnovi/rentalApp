<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected $token;   
    protected $from;

    public function __construct()
    {
        $this->token = config('services.sparrow.token'); // Add to env file
        $this->from = config('services.sparrow.from');   // Sender ID
    }

    public function sendOtp($phone, $otp)
    {
        $response = Http::post('https://api.sparrowsms.com/v2/sms/', [
            'token' => $this->token,
            'from' => $this->from,
            'to' => $phone,
            'text' => "Your OTP is: $otp",
        ]);

        if ($response->successful()) {
            return true;
        }

        return false;
    }
}
