<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function test(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('yby396@gmail.com')  // MUST match email in MAILER_DSN
                ->to('tonadresse@gmail.com')
                ->subject('Test Email')
                ->text('This is a test email from Symfony Mailer')
                ->html('<p>This is a test email from <strong>Symfony Mailer</strong></p>');

            $mailer->send($email);

            return new Response('Email sent successfully!');
        } catch (\Exception $e) {
            return new Response('Failed to send email: ' . $e->getMessage(), 500);
        }
    }
}