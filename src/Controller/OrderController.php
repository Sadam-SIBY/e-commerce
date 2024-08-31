<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id=>$quantity){

            $cartWithData[]= [
                'product'=>$productRepository->find($id), 
                'quantity'=>$quantity
            ];
        }
        $total = array_sum(array_map(function($items){

            return $items['product']->getPrice() * $items['quantity'];

        }, $cartWithData));

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total'=>$total
        ]);
    }
}
