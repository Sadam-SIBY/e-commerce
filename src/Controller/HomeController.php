<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods:['GET'])]
    public function index(ProductRepository $productRepository): Response
    {

        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findBy([], ['id'=>"DESC"])
        ]);
    }

    #[Route('/product/{id}/show', name: 'app_home_product_show', methods:['GET'])]
    public function show(Product $product, ProductRepository $productRepository): Response
    {
       $lastProducts = $productRepository->findBy([], ['id'=>'DESC'], 5);

        return $this->render('home/show.html.twig', [
            'product' => $product,
            'products' => $lastProducts
        ]);
    }
}
