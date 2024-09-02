<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderProducts;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request,
        SessionInterface $session, 
        ProductRepository $productRepository,
        EntityManagerInterface $em,
        Cart $cart,
    ): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($order->isPayOnDelivery()){
                if(!empty($data['total'])){
                    $order->setTotalPrice($data['total']);
                    $order->setCreatedAt(new \DateTimeImmutable());

                    $em->persist ($order);
                    $em->flush();
                    
                    foreach($data["cart"] as $value){
                        $orderProduct = new OrderProducts();
                        $orderProduct->setOrder($order);
                        $orderProduct->setProduct($value['product']);
                        $orderProduct->setQuantity($value["quantity"]) ;

                        $em->persist ($orderProduct);
                        $em->flush();
                    }
                }
                $session->set('cart', []);
                return $this->redirectToRoute('order_ok_message');

            }
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total'=>$data['total']
        ]);
    }

     
    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city):Response
    {
        $cityShippingPrice = $city->getShippingCost();
        return new Response (json_encode (['status'=>200, "message"=>'on', 'content'=>$cityShippingPrice]));
    }


    #[Route('/order-ok-message', name: 'order_ok_message')]
    public function orderMessage():Response
    {

       return $this->render('order/order_message.html.twig');
    }

}
