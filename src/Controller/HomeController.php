<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SubCategory;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Container7mxRQg4\getSubCategoryRepositoryService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods:['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository, Request $request,
    PaginatorInterface $paginator): Response
    {
        $data = $productRepository->findBy([], ['id'=>"DESC"]);
        $products = $paginator->paginate($data,
            $request->query->getInt('page', 1), 8);

        return $this->render('home/index.html.twig', [
            'products' =>  $products,
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/product/{id}/show', name: 'app_home_product_show', methods:['GET'])]
    public function show(Product $product, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
       $lastProducts = $productRepository->findBy([], ['id'=>'DESC'], 5);

        return $this->render('home/show.html.twig', [
            'product' => $product,
            'products' => $lastProducts,
            'categories'=>$categoryRepository->findAll()
        ]);
    }

    #[Route('/product/subcategory{id}/filter', name: 'app_home_product_filter', methods:['GET'])]
    public function filter($id, SubCategoryRepository $subCategoryRepository, CategoryRepository $categoryRepository): Response
    {
      
        $products = $subCategoryRepository->find($id)->getProducts();
        $subCategory = $subCategoryRepository->find($id);
        return $this->render('home/filter.html.twig', [
            'products'=> $products, 
            'subCategory'=> $subCategory,
            'categories'=>$categoryRepository->findAll()
        ]);
    }
}
