<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;

class MailjetService
{
    private $client;

    public function __construct(string $apiKey, string $secretKey)
    {
        $this->client = new Client($apiKey, $secretKey, true, ['version' => 'v3.1']);
    }

    public function sendEmail(string $fromEmail, string $fromName, string $toEmail, string $subject, string $text)
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $fromEmail,
                        'Name' => $fromName,
                    ],
                    'To' => [
                        [
                            'Email' => $toEmail,
                            'Name' => $toEmail
                        ]
                    ],
                    'Subject' => $subject,
                    'TextPart' => $text,
                ]
            ]
        ];

        $response = $this->client->post(Resources::$Email, ['body' => $body]);
        return $response->getStatus();
    }
}
