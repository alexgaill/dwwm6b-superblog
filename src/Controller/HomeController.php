<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route("/", name:"home")]
    public function home(): Response
    {
        return $this->render("home.html.twig");
    }
}