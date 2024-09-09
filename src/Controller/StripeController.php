<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Service\Cart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/pay/success', name: 'app_stripe_success')]
    public function success(
        SessionInterface $session, 
        Cart $cart,
    ): Response {
        // Récupérer les données du panier
        $data = $cart->getCart($session);
        $selectedCity = '1'; //  ville sélectionnée
    
        return $this->render('stripe/index.html.twig', [
            'total' => $data['total'],
            'selected_city' => $selectedCity, 
        ]);
    }
    
    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/stripe/notify', name: 'app_stripe_notify')]
    public function stripeNotify(Request $request): Response
    {
       Stripe::setApiKey($_SERVER['STRIPE_SECRET']);

       $endpoint_secret = 'whsec_5680852ce4cd4c0962802ef13583176e91302bd5d55a6bb0ce73c43451f63e6a';

       $payload = $request->getContent();
       $sig_header = $request->headers->get('stripe-signature');
       $event = null;

       try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        }catch(\UnexpectedValueException $e){
            return new Response ('payload invalide', 400);
        }
        catch(\Stripe\Exception\SignatureVerificationException $e){
            return new Response ('Signature invalide');
        }

        switch ($event->type){
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $fileName = 'stripe-details-'.uniqid().'txt';
                file_put_contents($fileName, $paymentIntent);
                break;
            
            case 'payment_method.attached':
                $payementMethod = $event->data->object;
                break;
            default :
                break;
        }
       return new Response ('evenement reçu', 200);
    }
}
