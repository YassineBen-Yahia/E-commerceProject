<?php

namespace App\Controller;

use App\Entity\Cart;
use Stripe\Stripe;
use Stripe\PaymentLink;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/create-payment-link/{id}/{total}', name: 'create_payment_link')]
    public function createPaymentLink($id,$total): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {

            $price = \Stripe\Price::create([
                'unit_amount' => $total*100,
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Total Price',
                ]
            ]);


            $paymentLink = PaymentLink::create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => ['url' => 'http://localhost:8000/payment/success/' . $id ],
                ],
            ]);


            return $this->redirect($paymentLink->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {

            return new Response('Error creating payment link: ' . $e->getMessage(), 500);
        }
    }

    #[Route('/payment/success/{id}', name: 'payment_success')]
    public function paymentSuccess($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $this->addFlash("success","Payment successful! Your transaction was completed.");
        return $this->redirectToRoute('app_place_order',['id' => $id]);

    }
}
