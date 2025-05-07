<?php

namespace App\Controller;

use App\Entity\AdminProfile;
use App\Entity\Utilisateur;
use App\Service\UtilisateurService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class UtilisateurController extends AbstractController
{

    private UtilisateurService $utilisateurService;
    public function __construct(UtilisateurService $utilisateurService){
        $this->utilisateurService = $utilisateurService;
    }
    #[Route('/users/{role?ROLE_USER}', name: 'users.admin')]
    public function usersList(ManagerRegistry $doctrine, $role): Response{

        $users = $this->utilisateurService->getUsersByRole($role);

        return $this->render('admin_view/users-list.html.twig', [
            'users' => $users,
            'role' => $role,
        ]);

    }

    #[Route('/users/makeAdmin/{id<\d+>}', name: 'make_admin')]
    public function makeAdmin(Utilisateur $utilisateur = null, ManagerRegistry $doctrine,$id): Response
    {
        if (!$utilisateur) {
            $this->addFlash('danger', "Utilisateur introuvable.");
            return $this->redirectToRoute('users.admin', ['role' => 'ROLE_USER']);
        }

        $roles = $utilisateur->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $this->utilisateurService->promoteUserToAdmin($utilisateur);

            $this->addFlash('success', "L'utilisateur a été promu administrateur avec succès.");
        } else {
            $this->addFlash('info', "L'utilisateur est déjà un administrateur.");
        }

        return $this->redirectToRoute('users.admin', ['role' => 'ROLE_USER']);
    }

/*    #[Route('/stats', name: 'stats.admin')]
    public function stats(ManagerRegistry $doctrine): Response{
        $repository = $doctrine->getRepository(Utilisateur::class);
        $users = $repository->findAll();
        $totalUsers = count($users);
        $totalAdmins = count($repository->findByRole('ROLE_ADMIN'));
        $totalClients = count($repository->findByRole('ROLE_CLIENT'));

        return $this->render('admin/stats.html.twig', [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalClients' => $totalClients,
        ]);
    }*/

/*    #[Route('/makeAdmin/{id<\d+>}', name: 'make_admin')]
    public function makeAdmin(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $repository = $doctrine->getRepository(Utilisateur::class);

        $utilisateur = $repository->find($id);

        if (!$utilisateur) {
            $this->addFlash('danger', "Utilisateur introuvable.");
            return $this->redirectToRoute('users.admin', ['role' => 'ROLE_USER']);
        }

        $roles = $utilisateur->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $utilisateur->setRoles($roles);
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur a été promu administrateur avec succès.");
        } else {
            $this->addFlash('info', "L'utilisateur est déjà un administrateur.");
        }

        return $this->redirectToRoute('users.admin', ['role' => 'ROLE_USER']);
    }*/

}
