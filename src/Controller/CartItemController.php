<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/update-cart-item/{id}', name: 'app_cart_update_item')]
    public function updateCartItem(CartItem $cartItem, Request $request, EntityManagerInterface $entityManager): Response
    {
        $newQuantity = (int) $request->request->get('quantity');
        $oldQuantity = $cartItem->getQuantité();

        if ($newQuantity <= 0) {
            $this->addFlash('error', 'Quantity must be greater than zero.');
            return $this->redirectToRoute('app_cart');
        }

        // Get the product associated with this cart item
        $produit = $cartItem->getProduits()->first();

        if (!$produit) {
            throw $this->createNotFoundException('Product not found in cart item');
        }

        // Calculate stock adjustment
        $stockDiff = $oldQuantity - $newQuantity;
        $newStock = $produit->getStock() + $stockDiff;

        // Check if we have enough stock
        if ($newStock < 0) {
            $this->addFlash('error', 'Not enough products in stock.');
            return $this->redirectToRoute('app_cart');
        }

        // Update the product stock
        $produit->setStock($newStock);

        // Update the cart item quantity
        $cartItem->setQuantité($newQuantity);

        $entityManager->persist($produit);
        $entityManager->persist($cartItem);
        $entityManager->flush();

        $this->addFlash('success', 'Cart updated successfully.');
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/update/{id}', name: 'app_update_item')]
    public function indexUpdate( CartItem $cartItem=null): Response
    {
//
        return $this->render('cart_item/update.html.twig', [
            'controller_name' => 'CartItemController',
            'cartItem' => $cartItem,
        ]);
    }
}
