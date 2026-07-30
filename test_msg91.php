<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mobileNumber = '9999999999';
$otp = '123456';
$authKey = config('services.msg91.auth_key');

$mobileNumber = '91' . preg_replace('/[^0-9]/', '', $mobileNumber);

$response = Illuminate\Support\Facades\Http::withHeaders([
    'authkey' => $authKey,
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

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
