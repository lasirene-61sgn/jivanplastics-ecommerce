<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91Service
{
    protected $authKey;
    protected $senderId;
    protected $templateId;

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->senderId = config('services.msg91.sender_id');
        $this->templateId = config('services.msg91.template_id');
    }

    /**
     * Send OTP via MSG91
     *
     * @param string $mobileNumber
     * @param string $otp
     * @return bool
     */
    public function sendOtp($mobileNumber, $otp)
    {
        if (empty($this->authKey)) {
            Log::warning('MSG91 Auth Key is not set. OTP to ' . $mobileNumber . ' was not sent. OTP: ' . $otp);
            return true; // Simulate success for local testing if no key
        }

        // Standardize mobile number (assuming India +91 if length is 10)
        $mobileNumber = preg_replace('/[^0-9]/', '', $mobileNumber);
        if (strlen($mobileNumber) == 10) {
            $mobileNumber = '91' . $mobileNumber;
        }

        try {
            $response = Http::withHeaders([
                'authkey' => $this->authKey,
                'accept' => 'application/json',
                'content-type' => 'application/json'
            ])->post('https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/', [
                'integrated_number' => '919360777089',
                'content_type' => 'template',
                'payload' => [
                    'messaging_product' => 'whatsapp',
                    'type' => 'template',
                    'template' => [
                        'name' => 'vr_1vcode',
                        'language' => [
                            'code' => 'en',
                            'policy' => 'deterministic'
                        ],
                        'namespace' => 'bc3735fb_a2e9_4e83_8b62_377bca25c09f',
                        'to_and_components' => [
                            [
                                'to' => [
                                    $mobileNumber
                                ],
                                'components' => [
                                    'body_1' => [
                                        'type' => 'text',
                                        'value' => (string)$otp
                                    ],
                                    'button_1' => [
                                        'subtype' => 'url',
                                        'type' => 'text',
                                        'value' => (string)$otp
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                // The WhatsApp API might return different success structure, usually has status or success or just 200 OK
                $result = $response->json();
                if (isset($result['hasError']) && $result['hasError'] === true) {
                    Log::error('MSG91 WhatsApp API Error: ' . json_encode($result));
                    return false;
                }
                return true;
            }

            Log::error('MSG91 HTTP Error: ' . $response->body());
            return false;
            
        } catch (\Exception $e) {
            Log::error('MSG91 Exception: ' . $e->getMessage());
            return false;
        }
    }
}
