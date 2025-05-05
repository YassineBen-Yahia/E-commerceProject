<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Commande;
use App\Form\CheckoutForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends AbstractController
{
    #[Route('/checkout/{cartId}', name: 'app_checkout')]

    public function index(Cart $cart=null): Response
    {
        $cartItems=[];
        if($cart){
            $cartItems = $cart->getCartItems();
        }

        return $this->render('checkout/index.html.twig', [
            'cartItems' => $cartItems,

        ]);
    }
}
