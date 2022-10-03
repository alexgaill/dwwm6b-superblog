<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'show_user', methods:['GET'], requirements:['id' => "\d+"])]
    public function show(int $id, ManagerRegistry $manager): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $manager->getRepository(User::class)->find($id) ?? 
                        throw $this->createNotFoundException("Cet utilisateur n'existe pas")
        ]);
    }
}
