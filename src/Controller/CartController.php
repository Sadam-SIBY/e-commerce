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
        
        $total = array_sum(array_map(function($item){

            return $item['product']->getPrice() * $item['quantity'];

        }, $cartWithData));

        dd($total);
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

    //    return $this->render('cart/index.html.twig', [
       
    // ]);
    
    }
}
