<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use App\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class MailerController extends AbstractController
{
    private RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService){
        $this->registrationService = $registrationService;
    }

    #[Route('/verify/email', name: 'verify_email')]
    public function verifyUserEmail(Request $request, EmailVerifier $emailVerifier, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $emailVerifier->handleEmailConfirmation($request, $this->getUser());

            $user = $this->getUser();
            $user->setIsVerified(true);
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_index');
    }

    #[Route('/account/verify-email', name: 'app_account_verify_email')]
    public function verifyEmailPage(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->getUser();

        return $this->render('account/email.html.twig',[
            'isVerified' => $user->isVerified(),
        ]);
    }

    #[Route('/send-verification-email', name: 'app_send_verification_email', methods: ['POST'])]
    public function sendVerificationEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $user = $this->getUser();

        if ($user->isVerified()) {
            $this->addFlash('info', 'Your email is already verified.');
            return $this->redirectToRoute('app_account_verify_email');
        }

        $this->registrationService->sendConfirmationEmail($user);

        $this->addFlash('success', 'Verification email sent successfully.');

        return $this->redirectToRoute('app_account_verify_email');
    }
}