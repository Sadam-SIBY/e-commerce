<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderProducts;
use App\Service\StripePayment;
use Symfony\Component\Mime\Email;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    // public function __construct(private MailerInterface $mailer){}

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

                if(!empty($data['total'])){
                    $totalPrice = $data['total'] + $order->getCity()->getShippingCost();
                    $order->setTotalPrice($totalPrice);
                    $order->setCreatedAt(new \DateTimeImmutable());

                    $order->setPaymentCompleted(0);

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

                    if($order->isPayOnDelivery()){
                        $session->set('cart', []);

                    // Mail de confirmation de commande
                        // $html = $this->renderView('mail/orderConfirm.html.twig',[
                        //     'order'=>$order
                        // ]);

                        // $email = (new Email())
                        // ->from('FreshShop@gmail.com')
                        // ->to('to@gmail.com')
                        // ->subject('Confirmation de reception de la commande.')
                        // ->html($html);

                    // $this->mailer->send($email);
                    return $this->redirectToRoute('order_ok_message');

                    }

                    $payment = new StripePayment();
                    $shippingCost = $order->getCity()->getShippingCost();
                    $payment->startPayment($data, $shippingCost, $order->getId());
            
                    $stripeRedirectUrl = $payment->getStripeRedirectUrl();
            
                    return $this->redirect($stripeRedirectUrl);

                }
      
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total'=>$data['total']
        ]);
    }

    #[Route('/editor/order/{type}', name: 'app_orders_show')]
    public function getAllOrders($type, OrderRepository $orderRepository, Request $request, PaginatorInterface $paginator ){


        
        if ($type == 'is_completed'){
            $data = $orderRepository->findBy(['isCompleted'=>1],  ['id'=>"DESC"]);
        } 
        elseif ($type == 'pay-on-stripe-not-delivered'){
            $data = $orderRepository->findBy(['isCompleted'=>null, 'payOnDelivery'=>0, 'isPaymentCompleted'=>1],  ['id'=>"DESC"]);

        } 

            $orders = $paginator->paginate(
                $data = $orderRepository->findBy(['isCompleted'=>null, 'isPaymentCompleted'=>1],  ['id'=>"DESC"]),
                $request->query->getInt('page', 1),
                2);
        

       
        return $this->render('order/order.html.twig',
        [
            'orders'=> $orders
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city):Response
    {
        $cityShippingPrice = $city->getShippingCost();
        return new Response (json_encode (['status'=>200, "message"=>'on', 'content'=>$cityShippingPrice]));
    }


    #[Route('/editor/order/{id}/isCompleted/update', name: 'app_orders_is_completed_update')]
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $em):Response
    {
        $order = $orderRepository->find($id);
        $order->setCompleted(true);
        $em->flush();

        $this->addFlash('success', 'Modification effectuée avec succès');
        return  $this->redirectToRoute('app_orders_show');

    }
//Suppression de la commande
    #[Route('/editor/order/{id}/remove', name: 'app_orders_remove')]
    public function removeOrder($id, Order $order, EntityManagerInterface $em):Response
    {
   
        $em->remove($order);
        $em->flush();

        $this->addFlash('danger', 'Commande supprimée avec succès');
        return  $this->redirectToRoute('app_orders_show');

    }

    #[Route('/order-ok-message', name: 'order_ok_message')]
    public function orderMessage():Response
    {

       return $this->render('order/order_message.html.twig');
    }

}
