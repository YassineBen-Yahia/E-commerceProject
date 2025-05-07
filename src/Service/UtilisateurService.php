<?php

namespace App\Service;

use App\Entity\AdminProfile;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurService
{
    private ManagerRegistry $doctrine;
    private EntityManagerInterface $entityManager;
    public function __construct(ManagerRegistry $doctrine,EntityManagerInterface $entityManager){
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
    }
    public function getUsersByRole($role){
        $repository = $this->doctrine->getRepository(Utilisateur::class);
        return $repository->findByRole($role);
    }
    public function promoteUserToAdmin(Utilisateur $utilisateur):void{

            $utilisateur->setRoles(['ROLE_ADMIN']);

            $adminProfile = new AdminProfile();
            $adminProfile->setUtilisateur($utilisateur);

            $this->entityManager->persist($utilisateur);
            $this->entityManager->persist($adminProfile);
            $this->entityManager->flush();



    }
}