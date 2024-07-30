<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class CategoryController extends AbstractController

{
   
    #[Route('/category', name: 'app_categories')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/new', name:'app_category_new')]
    public function addCategory(EntityManagerInterface $em, Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest ($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', "Votre catégorie a été ajoutée avec succès");
            return $this->redirectToRoute('app_category');

        }

        return $this->render('category/new.html.twig', [
            'form'=>$form->createView()
            
        ]);
    }

    #[Route('category/{id}/update', name:'app_category_update')]
    public function update(Category $category, EntityManagerInterface $em, Request $request): Response
    {

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest ($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', "Votre catégorie a été modifiée avec succès");
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/update.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    #[Route('category/{id}/delete', name:'app_category_delete')]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('danger', "Votre catégorie a été supprimée avec succès");
        return $this->redirectToRoute('app_category');
    }
}
