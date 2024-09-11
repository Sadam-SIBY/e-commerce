<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StripePayment
{

    private $redirectUrl;

    public function __construct()    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET']);
        Stripe::setApiVersion('2024-06-20');
    }
  
    public function startPayment($cart, $shippingCost, $orderId ){

        $cartProducts = $cart['cart'];
        
        $products = [
            ['quantity'=> 1,
             'price'   => $shippingCost,
             'name'    => 'Frais de livraison'
            ]

        ];
        foreach ($cartProducts as $value){
                $productItem = [] ;
                $productItem['name'] = $value['product']->getName();
                $productItem['price'] = $value['product']->getPrice();
                $productItem['quantity'] = $value['quantity'];
                $products []= $productItem;
            };
       
        

        $session = Session::create([
            // Produits à payer
            'line_items'=>[
                array_map(fn(array $product)=>[
                'quantity' => $product['quantity'],
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    'unit_amount' => $product['price']*100
                ],
            ], $products)

            ],
            'mode'=>'payment',
            'cancel_url'=>'https://127.0.0.1:8000/pay/cancel',
            'success_url'=>'https://127.0.0.1:8000/pay/success',
            'billing_address_collection' =>'required',
            'shipping_address_collection' =>[
                'allowed_countries'=>['FR']
            ],
            "payment_intent_data"=>[
                'metadata'=>[
                    'orderId'=>$orderId
                ]
            ]
           
            ]);

            $this->redirectUrl = $session->url;

    }
    public function getStripeRedirectUrl(){
        return $this->redirectUrl;
    }


}