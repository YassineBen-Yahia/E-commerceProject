<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ClientProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WishListController extends AbstractController
{
    private ClientProfileRepository $clientProfileRepository;
    public function __construct(ClientProfileRepository $clientProfileRepository)
    {
        $this->clientProfileRepository = $clientProfileRepository;
    }

    #[Route('/wishList', name: 'app_wish_list')]
    public function index(): Response
    {
        $user = $this->getUser();
        $userProfile = $this->clientProfileRepository->findOneByUser($user);
        $wishList = $userProfile->getWishList();

        return $this->render('wish_list/index.html.twig', [
            'controller_name' => 'WishListController',
            'wishList' => $wishList,
        ]);
    }

    #[Route('/wish/list/remove/{id}', name: 'app_wish_list_remove_item')]
    public function removeItem(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userProfile = $this->clientProfileRepository->findOneByUser($user);
        $wishList = $userProfile->getWishList();

        // Assuming you have a method to find the item by ID


        $wishList->getProduits()->removeElement($produit);

        $entityManager->flush();


        return $this->redirectToRoute('app_wish_list');
    }

    #[Route('/AddToWishlist/{id}', name: 'app_Wishlist_add_item')]
    public function addToCart( Produit $produit,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userProfile = $this->clientProfileRepository->findOneByUser($user);
        $wishList = $userProfile->getWishList();
        $wishList->addProduit($produit);
        $entityManager->persist($wishList);
        $entityManager->flush();
        return $this->render('wish_list/index.html.twig', [
            'controller_name' => 'WishListController',
            'wishList' => $wishList,
        ]);


    }
}
