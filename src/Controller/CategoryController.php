<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * Comme l'on veut récupérer et afficher les informations d'une catégorie,
     * nous allons passer l'id de la catégorie à afficher dans l'url.
     * Pour récupérer un paramètre dans l'url, on l'entoure d'accolades.
     * On ajoute des précisions sur le paramètre que l'on récupère grâce à l'option requirements
     * Ici l'id doit obligatoirement être un nombre.
     */
    #[Route("/category/{id}", name:"single_category", requirements: ["id" => "\d+"], methods:["GET"])]
    public function single(int $id, ManagerRegistry $manager): Response
    {
        // On utilise la méthode find du Repository qui recherche 
        // une catégorie en fonction d'un id passé en paramètre
        $category = $manager->getRepository(Category::class)->find($id);

        return $this->render('category/single.html.twig', [
            'category' => $category
        ]);
    }
}
