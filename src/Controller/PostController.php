<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    #[IsGranted("ROLE_AUTHOR", message:"Seuls les auteurs peuvent accéder à cette page", statusCode: 403)]
    public function save(Request $request): Response
    {
        $post = new Post;
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les informations de l'image non mappée
            $picture = $form->get('picture')->getData(); // On récupère un objet File
            if ($picture) {
               
                // On attribue un nouveau nom à l'image
                // $picture->guessExtension() est une méthode permettant de récupérer l'extension du fichier
                $pictureName = md5(uniqid()).'.'. $picture->guessExtension();
                // On enregistre l'image sur le serveur
                try {
                    $picture->move(
                        $this->getParameter('upload_dir'),
                        $pictureName
                    );
                } catch (FileException $e) {
                    $this->addFlash("danger", "Une erreur est survenue durant l'enregistrement de l'image: " . $e->getMessage());
                }
                // On enregistre l'image en BDD
                $post->setPicture($pictureName);
            }
            // Associe l'utilisateur connecté
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTime);
            $om = $this->manager->getManager();
            $om->persist($post);
            $om->flush();
            $this->addFlash("success", $post->getTitle() . " a bien été créé");
            return $this->redirectToRoute("single_post", ['id' => $post->getId()]);
        }

        return $this->renderForm("post/save.html.twig", [
            'postForm' => $form
        ]);
    }

    #[Route("/post/{id}/update", name:"update_post", methods:['GET', 'POST'], requirements:['id' => "\d+"])]
    public function update(int $id, Request $request): Response
    {
        // isGranted est une méthode de l'AbstractController qui vérifie que l'utilisateur connecté possède un certain rôle
        if (!$this->isGranted("ROLE_AUTHOR")) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour modifier un article");
            return $this->redirectToRoute('home');
        }

        $post = $this->manager->getRepository(Post::class)->find($id);
        if (!$post) {
            $this->addFlash('danger', "L'article que vous souhaitez modifier n'existe pas");
            return $this->redirectToRoute('app_post');
        }
        
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les informations de l'image non mappée
            $picture = $form->get('picture')->getData(); // On récupère un objet File
            // Cas où pas de nouvelle image ajoutée
            if ($picture) {
                if ($post->getPicture()) {
                    // Cas où l'ancienne image est à remplacer
                    unlink($this->getParameter('upload_dir') . '/' . $post->getPicture());
                }
                // Cas où pas d'ancienne image
                $pictureName = md5(uniqid()).'.'. $picture->guessExtension();
                try {
                    $picture->move(
                        $this->getParameter('upload_dir'),
                        $pictureName
                    );
                    $this->addFlash('success', "Image modifiée correctement");
                } catch (FileException $e) {
                    $this->addFlash("danger", "Une erreur est survenue durant l'enregistrement de l'image: " . $e->getMessage());
                }
                $post->setPicture($pictureName);
            }
            $om = $this->manager->getManager();
            $om->persist($post);
            $om->flush();
            $this->addFlash('success', "Article mis à jour correctement");
            return $this->redirectToRoute('single_post', ['id' => $post->getId()]);
        }

        return $this->renderForm('post/update.html.twig', [
            'postForm' => $form
        ]);
    }

    #[Route('/post/{id}/delete', name:'delete_post', requirements:['id' => "\d+"], methods:['GET'])]
    public function delete (int $id): Response
    {
        // Refuse l'accès à toute personne n'ayant pas le rôle définit et la renvoie sur une page d'erreur
        $this->denyAccessUnlessGranted("ROLE_ADMIN", 'Accès refusé', 'Accès limité aux admin');
        $post = $this->manager->getRepository(Post::class)->find($id);
        if ($post) {
            $om = $this->manager->getManager();
            $om->remove($post);
            $om->flush();
        }
        $this->addFlash('info', "L'article a bien été supprimé");
        return $this->redirectToRoute('app_post');
    }
}
