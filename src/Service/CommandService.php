<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Commande;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class CommandService
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer){
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
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

   public function getCommandByUser(Utilisateur $user){
       return $this->entityManager->getRepository(Commande::class)->findCommandByUser($user);
   }

    public function sendOrderEmail(Utilisateur $user, Cart $cart){
        $cartItems = $cart->getCartItems();
        $itemsList = [];
        $total = 0;

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduit();
            $subtotal = $product->getPrice() * $cartItem->getQuantité();
            $total += $subtotal;

            $itemsList[] = [
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $cartItem->getQuantité(),
                'subtotal' => $subtotal
            ];
        }
        $email = (new TemplatedEmail())
            ->from('echripc@gmail.com')
            ->to($user->getEmail())
            ->subject('Your Order Has Been Confirmed')
            ->htmlTemplate('command/order_confirmation.html.twig')
            ->context([
                'user' => $user,
                'items' => $itemsList,
                'total' => $total,
                'date' => new \DateTime()
            ]);

        $this->mailer->send($email);
    }
}