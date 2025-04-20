<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\EmailParams;

class Inquiry extends Controller
{
    public function sendMessage()
    {
        $session = Services::session();
        $validation = Services::validation();
        $loggedUser = $session->get('LoggedUserData');

        if (empty($loggedUser['email'])) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'success' => false,
                    'message' => 'Cannot send email: no sender address available.',
                ]);
        }

        // 1) Grab JSON or form-data
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // 2) Validation rules
        $rules = [
            'facilitator_email' => 'required|valid_email',
            'subject' => 'required|min_length[3]|max_length[255]',
            'message' => 'required|min_length[5]',
            // optional if you ever want to pass them
            'facilitator_name' => 'permit_empty',
            'inquiry_date' => 'permit_empty',
        ];
        $validation->setRules($rules);

        if (!$validation->run($input)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validation->getErrors(),
                ]);
        }

        try {
            // 3) SDK instantiation
            $mailerSend = new MailerSend(); // reads MAILERSEND_API_KEY

            // 4) Recipient
            $toEmail = $input['facilitator_email'];
            $toName = $input['facilitator_name'] ?? $toEmail;
            $recipients = [new Recipient($toEmail, $toName)];

            // 5) Sender info
            $senderEmail = $loggedUser['email'];
            $senderName = $loggedUser['name'] ?? $senderEmail;

            // 6) Personalization — must match your template variables
            $personalization = [
                new Personalization(
                    $toEmail,
                    [
                        'sender_name' => $senderName,
                        'inquiry_date' => $input['inquiry_date'] ?? date('Y-m-d'),
                        'inquiry_message' => $input['message'],
                        'facilitator_name' => $toEmail,
                    ]
                ),
            ];

            // 7) Build & send
            $emailParams = (new EmailParams())
                ->setFrom('no-reply@test-xkjn41mmzep4z781.mlsender.net')
                ->setFromName('Wazte Inquiry Bot')
                ->setReplyTo($senderEmail)
                ->setReplyToName($senderName)
                ->setRecipients($recipients)
                ->setTemplateId('jy7zpl99qo5l5vx6')
                ->setSubject($input['subject'])
                ->setPersonalization($personalization);

            $mailerSend->email->send($emailParams);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Inquiry sent using template!',
            ]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Email sending failed: ' . $e->getMessage(),
                ]);
        }
    }
}
