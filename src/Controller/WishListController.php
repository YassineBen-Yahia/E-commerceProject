<?php

namespace App\Controller;

use App\Entity\Produit;

use App\Service\WhishListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WishListController extends AbstractController
{
    private WhishListService $wishListService;
    public function __construct(WhishListService $wishListService){
        $this->wishListService = $wishListService;
    }

    #[Route('/wishList', name: 'app_wish_list')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $wishList = $this->wishListService->getWishListByUser($this->getUser());
        return $this->render('wish_list/index.html.twig', [
            'controller_name' => 'WishListController',
            'wishList' => $wishList,
        ]);
    }

    #[Route('/wish/list/remove/{id}', name: 'app_wish_list_remove_item')]
    public function removeItem(Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $wishList = $this->wishListService->getWishListByUser($this->getUser());

        $this->wishListService->removeWishlistItem($wishList,$produit);


        return $this->redirectToRoute('app_wish_list');
    }

    #[Route('/AddToWishlist/{id}', name: 'app_Wishlist_add_item')]
    public function addToCart( Produit $produit,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $wishList = $this->wishListService->getWishListByUser($this->getUser());

        $this->wishListService->addProductToWishlist($wishList,$produit);
        return $this->render('wish_list/index.html.twig', [
            'controller_name' => 'WishListController',
            'wishList' => $wishList,
        ]);


    }
}
