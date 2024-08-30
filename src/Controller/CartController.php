<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
        {
            
        }

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id=>$quantity){

            $cartWithData[]= [
                'product'=>$this->productRepository->find($id), 
                'quantity'=>$quantity
            ];
        }
        
        $total = array_sum(array_map(function($items){

            return $items['product']->getPrice() * $items['quantity'];

        }, $cartWithData));

   
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' =>$total
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
