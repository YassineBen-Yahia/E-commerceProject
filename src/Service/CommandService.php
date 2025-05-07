<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;


class CommandService
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;

    }
   public function calculateTotal(Cart $cart): float{
       $cartItems = $cart->getCartItems();
       $total = 0;
       foreach ($cartItems as $cartItem) {

           $total += $cartItem->getProduit()->getPrice() * $cartItem->getQuantité();
       }
       return $total;

   }

   public function manageCommand(Cart $cart){
       $cartItems = $cart->getCartItems();
       $commande = new Commande();
       $commande->setUtilisateur($cart->getUtilisateur()->getUtilisateur());
       foreach ($cartItems as $cartItem) {
           $commande->addCartItem($cartItem);
           $cartItem->setCommande($commande);
           $cartItem->setCart(null);

       }


       $this->entityManager->persist($commande);
       $this->entityManager->flush();
   }





}