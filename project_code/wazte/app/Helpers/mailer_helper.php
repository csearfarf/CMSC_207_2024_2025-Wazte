<?php

use GuzzleHttp\Client;

/**
 * Sends an email using the MailerSend API.
 */
function sendEmailViaMailerSend(string $to, string $subject, string $htmlBody, string $replyTo = null): bool
{
    $apiKey = getenv('MAILERSEND_API_KEY'); // or use .env
    $senderEmail = getenv('MAILERSEND_FROM_EMAIL');
    $senderName = getenv('MAILERSEND_FROM_NAME');

    $client = new Client([
        'base_uri' => 'https://api.mailersend.com/v1/',
        'headers' => [
            'Authorization' => "Bearer $apiKey",
            'Content-Type' => 'application/json',
        ]
    ]);

    $body = [
        'from' => [
            'email' => $senderEmail,
            'name' => $senderName
        ],
        'to' => [['email' => $to]],
        'subject' => $subject,
        'html' => $htmlBody,
    ];

    if ($replyTo) {
        $body['reply_to'] = [
            'email' => $replyTo
        ];
    }

    try {
        $response = $client->post('email', [
            'json' => $body
        ]);

        return $response->getStatusCode() === 202;
    } catch (\Exception $e) {
        log_message('error', 'MailerSend error: ' . $e->getMessage());
        return false;
    }
}
