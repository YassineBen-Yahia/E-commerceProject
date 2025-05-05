<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartItemController extends AbstractController
{
//    private ProduitRepository $produitRepository;
//    public function __construct(ProduitRepository $produitRepository)
//    {
//        $this->produitRepository = $produitRepository;
//    }

    #[Route('/CartItem/{id}', name: 'app_cart_item')]
    public function index( Produit $produit=null): Response
    {
//
        return $this->render('cart_item/index.html.twig', [
            'controller_name' => 'CartItemController',
            'produit' => $produit,
        ]);
    }
    #[Route('/Add_CartItem/{id}', name: 'app_cart_add_item')]
    public function addToCart( Produit $produit=null, Request $request,EntityManagerInterface $entityManager): Response
    {
        $quantity = $request->request->get('quantity');
        $cartItem = new CartItem();
        $cartItem->setUtilisateur($this->getUser());

        if ($produit) {
            $produit->setStock($produit->getStock()-$quantity);
            $cartItem->addProduit($produit);
            $cartItem->setQuantité($quantity);
            $cartItem->setCart($this->getUser()->getCart());
            $this->getUser()->getCart()->addCartItem($cartItem);

            $entityManager->persist($produit);
            $entityManager->persist($cartItem);
            $entityManager->flush();
        } else {
            throw $this->createNotFoundException('Produit not found');
        }
        return $this->render('cart_item/index.html.twig', [
            'controller_name' => 'CartItemController',
            'produit' => $produit,
        ]);
    }
}
