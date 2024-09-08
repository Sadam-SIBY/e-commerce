<?php

namespace App\Controller;

use App\Service\Cart;
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
 
        return $this->render('stripe/index.html.twig', [
            'total' => $data['total'],  
          
        ]);
    }
    
    #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
}
