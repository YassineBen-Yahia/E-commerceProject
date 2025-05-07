<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ClientProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    private ClientProfileRepository $clientProfileRepository;
    private CartItemRepository $cartItemRepository;
    public function __construct(ClientProfileRepository $clientProfileRepository, CartItemRepository $cartItemRepository)
    {
        $this->clientProfileRepository = $clientProfileRepository;
        $this->cartItemRepository = $cartItemRepository;
    }
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // Assuming every registered user has a cart
        $total= 0;
        $clientProfile = $this->clientProfileRepository->findOneByUser($this->getUser());
        $cart = $clientProfile->getCart();
        $cartItems = $cart->getCartItems();
        dump($cart);
        foreach ($cartItems as $cartItem) {

            $total += $cartItem->getProduit()->getPrice() * $cartItem->getQuantité();
        }
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart,
            'total' => $total,
        ]);
    }
    #[Route('/RemoveFromCart/{id}', name: 'app_cart_remove_item')]
    public function removeItem(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $clientProfile = $this->clientProfileRepository->findOneByUser($this->getUser());
        $cart = $clientProfile->getCart();
        if (!$cart) {
            $this->addFlash('error', 'No cart found for the current user.');
            return $this->redirectToRoute('app_index');
        }


        $cart->getCartItems()->removeElement($cartItem);
        $entityManager->remove($cartItem);
        $entityManager->flush();


        return $this->redirectToRoute('app_cart');

    }
}
