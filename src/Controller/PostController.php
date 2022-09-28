<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Ce controller a été généré grâce à la commande 'symfony console make:controller'
 * Cette commande Génère le controller avec une première méthode index et un template attribué
 */
class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(ManagerRegistry $manager): Response
    {
        return $this->render('post/index.html.twig', [
            // On refactorise notre code pour être le plus clair et léger possible
            'posts' => $manager->getRepository(Post::class)->findAll()
        ]);
    }
}
