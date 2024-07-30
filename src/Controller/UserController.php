<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/admin/user/{id}/to/editor', name: 'app_user_to_editor')]
    public function changeRole(EntityManagerInterface $em, User $user): Response
    {

        $user->setRoles(["ROLE_EDITOR", "ROLE_USER"]);
        $em->flush();

        $this->addFlash('success', 'Le rôle éditeur a été ajouté à votre utilisateur');

        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove/editor/role', name: 'app_user_remove_editor_role')]
    public function editorRoleRemove(EntityManagerInterface $em, User $user): Response
    {

        $user->setRoles([]);
        $em->flush();

        $this->addFlash('danger', 'Le rôle éditeur a été rétiré à votre utilisateur');

        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove', name: 'app_user_remove')]
    public function userRemove($id, EntityManagerInterface $em, UserRepository $userRepository): Response
    {

        $userFind = $userRepository->find($id);
        $em->remove($userFind );
        $em->flush();

        $this->addFlash('danger', 'Votre utilisateur a été supprimé');

        return $this->redirectToRoute('app_user');
    }
}
