<?php

namespace App\Controller;

use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        // Assuming every registered user has a cart
        $total= 0;
        $cartItems = $this->getUser()->getCart()->getCartItems();
        foreach ($cartItems as $cartItem) {
            $total += $cartItem->getProduits()->first()->getPrice() * $cartItem->getQuantité();
        }
        $cart = $this->getUser()->getCart();
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart,
            'total' => $total,
        ]);
    }
    #[Route('/RemoveFromCart/{id}', name: 'app_cart_remove_item')]
    public function removeItem(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        $cart = $this->getUser()->getCart();

        if (!$cart) {
            $this->addFlash('error', 'No cart found for the current user.');
            return $this->redirectToRoute('app_index');
        }


            $cart->removeCartItem($cartItem);
            $entityManager->flush();
            $this->addFlash('success', 'Item removed from cart successfully.');


        return $this->redirectToRoute('app_cart');

    }
}
