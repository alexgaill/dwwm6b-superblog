<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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

        // $post = $this->manager->getRepository(Post::class)->find($id);
        // if (!$post) {
        //     throw $this->createNotFoundException("L'article recherché n'existe pas");
        // }

        return $this->render('post/single.html.twig', [
            'post' => $this->manager->getRepository(Post::class)->find($id) ?? 
                        // Si on ne trouve pas l'article correspondant, on tombe de l'autre côté de ??
                        // et on lance une erreur Not Found
                      throw $this->createNotFoundException("L'article recherché n'existe pas")
        ]);
    }

    #[Route('/post/save', name:"add_post", methods:['GET', 'POST'])]
    public function save(Request $request): Response
    {
        $post = new Post;
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setCreatedAt(new \DateTime);
            $om = $this->manager->getManager();
            $om->persist($post);
            $om->flush();

            return $this->redirectToRoute("single_post", ['id' => $post->getId()]);
        }

        return $this->renderForm("post/save.html.twig", [
            'PostForm' => $form
        ]);
    }
}
