<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\ClientProfile;
use App\Entity\Utilisateur;
use App\Entity\WishList;
use App\Form\RegistrationForm;
use App\Security\EmailVerifier;
use App\Security\LoginAuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, EmailVerifier $emailVerifier): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $userPofile= new ClientProfile();
            $userPofile->setUtilisateur($user);
            $cart= new Cart();
            $cart->setUtilisateur($userPofile);
            $wishList = new WishList();
            $wishList->setUtilisateur($userPofile);
            $userPofile->setCart($cart);
            $userPofile->setWishList($wishList);
            $entityManager->persist($cart);
            $entityManager->persist($wishList);
            $entityManager->persist($user);
            $entityManager->flush();

            $emailVerifier->sendEmailConfirmation(
                'verify_email',
                $user,
                (new TemplatedEmail())
                    ->from('echripc@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $request->getSession()->invalidate();
            $security->login($user, LoginAuthAuthenticator::class, 'main');
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

        $emailVerifier->sendEmailConfirmation(
            'verify_email',
            $user,
            (new TemplatedEmail())
                ->from('echripc@gmail.com')
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $this->addFlash('success', 'A new verification email has been sent to your email address.');

        return $this->redirectToRoute('app_verify_email_notice');
    }

}
