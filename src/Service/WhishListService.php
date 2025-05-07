<?php

namespace App\Service;

use App\Entity\Produit;

use App\Entity\Utilisateur;
use App\Entity\WishList;
use App\Repository\ClientProfileRepository;
use Doctrine\ORM\EntityManagerInterface;

class WhishListService
{
    private ClientProfileRepository $clientProfileRepository;
    private EntityManagerInterface $entityManager;
    public function __construct(ClientProfileRepository $clientProfileRepository, EntityManagerInterface $entityManager)
    {
        $this->clientProfileRepository = $clientProfileRepository;
        $this->entityManager = $entityManager;
    }

    public function getWishListByUser(Utilisateur $user){
        $userProfile = $this->clientProfileRepository->findOneByUser($user);
        return $userProfile->getWishList();
    }

    public function removeWishlistItem(WishList $wishList,Produit $produit):void{
        $wishList->getProduits()->removeElement($produit);
        $this->entityManager->flush();
    }

    public function addProductToWishlist(WishList $wishList,Produit $produit):void{
        $wishList->addProduit($produit);
        $this->entityManager->persist($wishList);
        $this->entityManager->flush();
    }
}