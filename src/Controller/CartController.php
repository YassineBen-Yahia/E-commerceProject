<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ClientProfileRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    private CartService $cartService;

    public function __construct(CartService $cartService){
        $this->cartService = $cartService;
    }
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {$this->denyAccessUnlessGranted('ROLE_USER');
       $cart=$this->cartService->getCartByUser($this->getUser());

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart,
            'total' => $this->cartService->calculateTotal($cart),
        ]);
    }
    #[Route('/RemoveFromCart/{id}', name: 'app_cart_remove_item')]
    public function removeItem(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $cart=$this->cartService->getCartByUser($this->getUser());
        if (!$cart) {
            $this->addFlash('error', 'No cart found for the current user.');
            return $this->redirectToRoute('app_index');
        }


        $this->cartService->removeCartItem($cart,$cartItem);

        return $this->redirectToRoute('app_cart');

    }
}
