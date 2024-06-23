<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    #[Route('/api/users', name: 'api_user_index', methods: ['GET'])]
    public function apiIndex(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => 'user:read']);
    }


    #[Route('/api/users/{id}', name: 'api_user_show', methods: ['GET'])]
    public function apiShow(User $user): Response
    {
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    #[Route('/api/users', name: 'api_user_new', methods: ['POST'])]
    public function apiNew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setUserEmail($data['userEmail']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201, [], ['groups' => 'user:read']);
    }

    #[Route('/api/users/{id}', name: 'api_user_edit', methods: ['PUT'])]
    public function apiEdit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $user->setUserEmail($data['userEmail']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);

        $entityManager->flush();

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    #[Route('/api/users/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function apiDelete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new Response(null, 204);
    }
}
