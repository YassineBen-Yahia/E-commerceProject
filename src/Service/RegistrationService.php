<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\ClientProfile;
use App\Entity\Utilisateur;
use App\Entity\WishList;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationService
{
    private EntityManagerInterface $entityManager;
    private EmailVerifier $emailVerifier;
    private UserPasswordHasherInterface $userPasswordHasher;
    private Security $security;
    public function __construct(
        EntityManagerInterface $entityManager,
        EmailVerifier $emailVerifier,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->security = $security;
    }
    public function sendConfirmationEmail(Utilisateur $user){
        $this->emailVerifier->sendEmailConfirmation(
            'verify_email',
            $user,
            (new TemplatedEmail())
                ->from('echripc@gmail.com')
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

    }
    public function registerUser(Utilisateur $user,Request $request,$plainPassword){
        /** @var string $plainPassword */


        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
        $userPofile= new ClientProfile();
        $userPofile->setUtilisateur($user);
        $cart= new Cart();
        $cart->setUtilisateur($userPofile);
        $wishList = new WishList();
        $wishList->setUtilisateur($userPofile);
        $userPofile->setCart($cart);
        $userPofile->setWishList($wishList);
        $this->entityManager->persist($cart);
        $this->entityManager->persist($wishList);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendConfirmationEmail($user);

        $request->getSession()->invalidate();
        $this->security->login($user, 'security.authenticator.form_login.main');
    }


}