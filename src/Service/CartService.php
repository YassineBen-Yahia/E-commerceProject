<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;

use App\Repository\CartItemRepository;
use App\Repository\ClientProfileRepository;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CartService
{
    private ClientProfileRepository $clientProfileRepository;
    private CartItemRepository $cartItemRepository;
    private EntityManagerInterface $entityManager;
    public function __construct(ClientProfileRepository $clientProfileRepository, CartItemRepository $cartItemRepository,EntityManagerInterface $entityManager)
    {
        $this->clientProfileRepository = $clientProfileRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->entityManager = $entityManager;
    }
    public function getCartByUser(UserInterface $user): Cart{
        $clientProfile = $this->clientProfileRepository->findOneByUser($user);
        return $clientProfile->getCart();
    }

    public function calculateTotal(Cart $cart): float{
        $cartItems = $cart->getCartItems();
        $total = 0;
        foreach ($cartItems as $cartItem) {

            $total += $cartItem->getProduit()->getPrice() * $cartItem->getQuantité();
        }
        return $total;

    }
    public function removeCartItem(Cart $cart,CartItem $cartItem):void{
        $cart->getCartItems()->removeElement($cartItem);
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();
    }

}