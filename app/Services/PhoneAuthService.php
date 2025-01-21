<?php

namespace App\Services;

use Twilio\Rest\Client;

class PhoneAuthService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendOtp($phoneNumber)
    {
        $otp = rand(100000, 999999); // Generate a random OTP
        // Save the OTP in a cache or database
        // Use Twilio API to send SMS (just as an example)
        $this->twilio->messages->create($phoneNumber, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => "Your verification code is: $otp"
        ]);
        return $otp;
    }

    public function verifyOtp($inputOtp, $storedOtp)
    {
        return $inputOtp == $storedOtp;
    }
}
