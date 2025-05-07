<?php

namespace App\Controller;


use App\Entity\Utilisateur;

use App\Form\RegistrationForm;
use App\Security\EmailVerifier;
use App\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
    private RegistrationService $registrationService;
    public function __construct(RegistrationService $registrationService){
        $this->registrationService = $registrationService;
    }
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);
        $plainPassword = $form->get('plainPassword')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
           $this->registrationService->registerUser($user,$request,$plainPassword);
            return $this->redirectToRoute('app_verify_email_notice');
        }
        else{
            $errors = $form->getErrors();

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'errors' => $errors,
        ]);
    }

    #[Route('/verify-email-notice', name: 'app_verify_email_notice')]
    public function verifyEmailNotice(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('registration/verify_email_notice.html.twig');
    }

    #[Route('/resend-verification-email', name: 'app_resend_verification_email')]
    public function resendVerificationEmail(EmailVerifier $emailVerifier, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if ($user->isVerified()) {
            $this->addFlash('info', 'Your email is already verified.');
            return $this->redirectToRoute('app_index');
        }
        $this->registrationService->sendConfirmationEmail($user);

        $this->addFlash('success', 'A new verification email has been sent to your email address.');

        return $this->redirectToRoute('app_verify_email_notice');
    }

}
