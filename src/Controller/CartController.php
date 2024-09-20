<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Cart;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
        {
            
        }

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, Cart $cart, Security $security, Request $request, 
    AuthenticationUtils $authenticationUtils ): Response
    {
        $user = $security->getUser();
        $data = $cart->getCart($session);   

        if (!$user) {
        // l'URL  après connexion
        $request->getSession()->set('_security.main.target_path', $this->generateUrl('app_order'));
        
        return $this->redirectToRoute('app_login');
    }
        return $this->render('cart/index.html.twig', [
            'items' => $data['cart'],
            'total' =>$data['total'],
    
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_new', methods: ['GET'])]
    public function addToCart(int $id, SessionInterface $session): Response

    {
        $cart = $session->get('cart', []);
        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id]=1;
        }

        $session->set('cart', $cart);

       return $this->redirectToRoute('app_cart');
    
    }

    /**
     * Route suppression d'un produit au panier
     */ 


    #[Route('/cart/remove/{id}', name: 'app_cart_product_remove', methods: ['GET'])]
    public function removeToCart(int $id, SessionInterface $session): Response

    {
        $cart = $session->get('cart', []);
        if(!empty($cart[$id])){
            // supprimer du panier 
            unset($cart[$id]);
        }

        $session->set('cart', $cart);
        $this->addFlash('danger', "Le produit a été supprimé avec succès du panier");
       return $this->redirectToRoute('app_cart');
    
    }

    /**
     * Suppression du panier
     */

    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['GET'])]
    public function remove( SessionInterface $session): Response

    {
        $session->set('cart', []);
        $this->addFlash('danger', "Votre panier est vide");
       return $this->redirectToRoute('app_cart');
    
    }
}
