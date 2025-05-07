<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Commande;
use App\Form\CheckoutForm;
use App\Repository\CartRepository;
use App\Service\CommandService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class CommandController extends AbstractController
{
    private commandService $commandService;
    public function __construct(commandService $commandService)
    {
        $this->commandService = $commandService;
    }
    #[Route('/checkout/{id}', name: 'app_checkout')]

    public function index(Cart $cart): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->getUser()->isVerified()) {
            return $this->redirectToRoute('app_index');
        }

        if (!$cart) {
            $this->addFlash('error', 'No cart found for the current user.');
            return $this->redirectToRoute('app_index');
        }
        $total=$this->commandService->calculateTotal($cart);

        return $this->render('checkout/index.html.twig', [
            'cartItems' => $cart->getCartItems(),
            'cart'=>$cart,
            'total' => $total,

        ]);
    }
    #[Route('/checkout/order/{id}', name: 'app_place_order')]

    public function placeOrder(Cart $cart): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!$this->getUser()->isVerified()) {
            return $this->redirectToRoute('app_index');
        }

        if (!$cart) {
            $this->addFlash('error', 'No cart found for the current user.');
            return $this->redirectToRoute('app_index');
        }
        $this->commandService->manageCommand($cart);


        $this->addFlash('success', 'Your order has been placed successfully!');
        return $this->redirectToRoute('app_cart');
    }
}
