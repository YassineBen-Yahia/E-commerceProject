<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            throw $this->createAccessDeniedException('User not found.');
        }

        $form = $this->createForm(ProfileFormType::class, $user);

        $originalEmail = $user->getEmail();
        $form->handleRequest($request);
        $newEmail = $form->get('email')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if($newEmail !== $originalEmail) {
                $user->setIsVerified(false);
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
