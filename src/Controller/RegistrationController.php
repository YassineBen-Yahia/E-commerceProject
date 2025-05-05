<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\ClientProfile;
use App\Entity\Utilisateur;
use App\Entity\WishList;
use App\Form\RegistrationForm;
use App\Security\LoginAuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
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

            // do anything else you need here, like send an email

            return $security->login($user, LoginAuthAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
