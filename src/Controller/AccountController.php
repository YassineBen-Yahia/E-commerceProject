<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\AccountFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('User not found.');
        }

        $form = $this->createForm(AccountFormType::class, $user);
        $originalEmail = $user->getEmail();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newEmail = $form->get('email')->getData();
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('danger', 'Invalid password.');
                return $this->redirectToRoute('app_account');
            }

            if ($newEmail !== $originalEmail) {
                $user->setIsVerified(false);
            }

            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}