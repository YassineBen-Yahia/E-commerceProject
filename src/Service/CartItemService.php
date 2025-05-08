<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Produit;
use App\Repository\ClientProfileRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;


class CartItemService
{
    private ClientProfileRepository $clientProfileRepository;
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(ClientProfileRepository $clientProfileRepository, EntityManagerInterface $entityManager, Security $security)
    {
        $this->clientProfileRepository = $clientProfileRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    public function getQuantityFromRequest(Request $request): int {
        return (int)$request->request->get('quantity');
    }

    public function addCartItem(CartItem $cartItem, Produit $produit): void {
        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException('No authenticated user.');
        }

        $cartItem->setUtilisateur($user);

        $clientProfile = $this->clientProfileRepository->findOneByUser($user);
        $cart = $clientProfile->getCart();

        $cartItem->setCart($cart);
        $cart->addCartItem($cartItem);

        $this->entityManager->persist($produit);
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    public function getProductFromCartItem(CartItem $cartItem): ?Produit {
        return $cartItem->getProduit();
    }

    public function calculateStockAdjustment(Produit $produit,int $oldQuantity,int $newQuantity): float {
        $stockDiff = $oldQuantity - $newQuantity;
        return $produit->getStock() + $stockDiff;
    }

    public function updateProductStock(Produit $produit,int $newStock):void{
        $produit->setStock($newStock);
        $this->entityManager->persist($produit);
        $this->entityManager->flush();


    }
    public function updateCartItemQuantity(CartItem $cartItem,int $newQuantity):void{
        $cartItem->setQuantité($newQuantity);
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();

    }



}