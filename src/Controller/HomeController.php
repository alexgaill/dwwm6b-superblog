<?php
namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * En symfony un controller va contenir toute la logique métier à ajouter sur les pages.
 * Chaque méthode représente une page.
 * Chaque méthode doit obligatoirement retourner une response venant du composant HttpFoundation
 * 
 * Pour faciliter le lien avec le composant Routing, on utilise les attributs au-dessus de chaque méthode 
 * pour préciser les informations de la Route. 
 * On évite ainsi de tout écrire dans le fichier de config routes.yaml
 * 
 * L'AbstractController est le controller par défaut de Symfony. Il possède différentes méthodes très pratiques.
 */
class HomeController extends AbstractController{

    /**
     * Affiche la page Hello
     *
     * @return string
     */
    #[Route(path:"/hello", name:"hello")]
    public function hello(): Response 
    {
        return new Response("Hello World!");
    }

    #[Route("/bye", name: "bye")]
    public function bye(): Response
    {
        return new Response("<h1>Au revoir</h1>");
    }

    /**
     * On utilise l'injection de dépendance pour charger les dépendances dont on a besoin dans la méthode
     * Ici le ManagerRegistry permettant de charger le Repository en question
     *
     * @param ManagerRegistry $manager
     * @return Response
     */
    #[Route("/", name:"home")]
    public function home(ManagerRegistry $manager): Response
    {
        // On appelle la méthode getRepository qui charge le Repository rattaché à l'entité choisie
        $categories = $manager->getRepository(Category::class)->findAll();
        // dump permet de débugguer
        // dump($categories);

        // La méthode render permet de charger un template. Elle permet également de passer des informations au template
        return $this->render("home.html.twig", [
            'categories' => $categories
        ]);
    }
}