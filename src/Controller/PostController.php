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

    public function __construct(
        private ManagerRegistry $manager
    ){}

    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            // On refactorise notre code pour être le plus clair et léger possible
            'posts' => $this->manager->getRepository(Post::class)->findAll()
        ]);
    }

    #[Route('/post/{id}', name:'single_post', requirements:['id' => "\d+"], methods: ["GET"])]
    public function single(int $id): Response
    {
        return $this->render('post/single.html.twig', [
            'post' => $this->manager->getRepository(Post::class)->find($id)
        ]);
    }
}
