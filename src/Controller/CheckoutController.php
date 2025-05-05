<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Commande;
use App\Form\CheckoutForm;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Webmozart\Assert\Tests\StaticAnalysis\float;

final class CheckoutController extends AbstractController
{
    #[Route('/checkout/{id}', name: 'app_checkout')]

    public function index(Cart $cart): Response
    {
        if (!$cart) {
            $this->addFlash('error', 'No cart found for the current user.');
            return $this->redirectToRoute('app_index');
        }
            $cartItems = $cart->getCartItems();
             $total = 0;
            foreach ($cartItems as $cartItem) {

                $total += $cartItem->getProduit()->getPrice() * $cartItem->getQuantité();
            }


        return $this->render('checkout/index.html.twig', [
            'cartItems' => $cartItems,
            'cart'=>$cart,
            'total' => $total,

        ]);
    }
    #[Route('/checkout/order/{id}', name: 'app_place_order')]

    public function placeOrder(Cart $cart,EntityManagerInterface $em): Response
    {
        if (!$cart) {
            $this->addFlash('error', 'No cart found for the current user.');
            return $this->redirectToRoute('app_index');
        }
        else{
            $cartItems = $cart->getCartItems();
            $commande = new Commande();
            $commande->setUtilisateur($cart->getUtilisateur()->getUtilisateur());
            foreach ($cartItems as $cartItem) {
                $commande->addCartItem($cartItem);
                $cartItem->setCommande($commande);
                $cart->removeCartItem($cartItem);
                $em->remove($cartItem);
            }
            $em->persist($commande);
            $em->flush();
        }
        return $this->render('checkout/success.html.twig', [

        ]);
    }
}
