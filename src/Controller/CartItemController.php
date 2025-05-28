<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Produit;
use App\Service\CartItemService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartItemController extends AbstractController
{
    private CartItemService $cartItemService;
    public function __construct(CartItemService $cartItemService){
        $this->cartItemService = $cartItemService;
    }

    #[Route('/CartItem/{id}', name: 'app_cart_item')]
    public function index( Produit $produit=null): Response
    {
//
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('cart_item/index.html.twig', [
            'controller_name' => 'CartItemController',
            'produit' => $produit,
        ]);
    }
    #[Route('/Add_CartItem/{id}', name: 'app_cart_add_item')]
    public function addToCart( Produit $produit, Request $request,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $quantity = $this->cartItemService->getQuantityFromRequest($request);
        $cartItem = new CartItem();
        $cartItem->setUtilisateur($this->getUser());

        if ($produit) {
            if ($produit->getStock() < $quantity) {
                $this->addFlash('error', 'Not enough stock available.');
                return $this->redirectToRoute('app_cart');
            }
            else {
                $produit->setStock($produit->getStock() - $quantity);
                $cartItem->setProduit($produit);
                $cartItem->setQuantité($quantity);
            }

            $this->cartItemService->addCartItem($cartItem,$produit);
        } else {
            throw $this->createNotFoundException('Produit not found');
        }
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/update-cart-item/{id}', name: 'app_cart_update_item')]
    public function updateCartItem(CartItem $cartItem, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $newQuantity = $this->cartItemService->getQuantityFromRequest($request);
        $oldQuantity = $cartItem->getQuantité();

        if ($newQuantity <= 0) {
            $this->addFlash('error', 'Quantity must be greater than zero.');
            return $this->redirectToRoute('app_cart');
        }


        $produit = $this->cartItemService->getProductFromCartItem($cartItem);

        if (!$produit) {
            throw $this->createNotFoundException('Product not found in cart item');
        }


        $newStock = $this->cartItemService->calculateStockAdjustment($produit,$oldQuantity,$newQuantity);


        if ($newStock < 0) {
            $this->addFlash('error', 'Not enough products in stock.');
            return $this->redirectToRoute('app_cart');
        }


        $this->cartItemService->updateProductStock($produit,$newStock);

        $this->cartItemService->addCartItem($cartItem,$produit);

       $this->cartItemService->updateCartItemQuantity($cartItem,$newQuantity);
       return $this->redirectToRoute('app_cart');
    }
    #[Route('/update/{id}', name: 'app_update_item')]
    public function indexUpdate( CartItem $cartItem=null): Response
    {
//
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('cart_item/update.html.twig', [
            'controller_name' => 'CartItemController',
            'cartItem' => $cartItem,
        ]);
    }
}
