<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route("/category/save", name:"add_category", methods:["GET", "POST"])]
    public function save(Request $request, ManagerRegistry $manager): Response
    {
        // On créé un objet vide que l'on veut enregistrer
        $category = new Category;
        // On génère le formulaire auquel on rattache la catégorie
        $form = $this->createFormBuilder($category)
                // On ajoute un input dédié au champs name
                ->add('name', TextType::class, [
                    'label' => 'Nom de la catégorie',
                    'attr' => [
                        'class' => "form-floating",
                        'placeholder' => "Nom de la catégorie"
                    ]
                ])
                // On ajoute le bouton de soumission
                ->add('submit', SubmitType::class, [
                    'label' => "Ajouter",
                    'attr' => [
                        'class' => "btn btn-primary"
                    ]
                ])
                // On génère le formulaire une fois fini
                ->getForm();
        
        // On rattache le composant HttpFoundation/Request au formulaire pour qu'il puisse récupérer les données
        // une fois le formulaire soumis
        $form->handleRequest($request);
        // On vérifie que le formulaire a été soumis et que les données sont valides
        // avant de continuer l'enregistrement.
        if ($form->isSubmitted() && $form->isValid()) {
            $om = $manager->getManager();
            $om->persist($category);
            $om->flush();

            return $this->redirectToRoute("home");
        }

        // Lorsqu'on passe un formulaire au template on doit utiliser la méthode renderForm
        // On peut retrouver la méthode render mais dans ce cas, on passe en paramètre pour le form
        // $form->createView()
        return $this->renderForm("category/save.html.twig", [
            'CategoryForm' => $form
        ]);
    }
}
