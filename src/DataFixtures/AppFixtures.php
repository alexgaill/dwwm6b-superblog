<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        // On charge le générateur de Faker
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->createCategory($manager);
        $this->createPost($manager);
        // On exécute toutes els requêtes en attente puis on vide la file.
        $manager->flush();
    }

    private function createCategory (ObjectManager $manager): void
    {
        // On lance une boucle pour créer 10 catégories
        for ($i=0; $i < 10; $i++) { 
            // On instancie l'entité Category pour générer nos catégories
            $category = new Category();
            // On donne un nom à la catégorie généré par Faker
            $category->setName($this->faker->words(3, true));
            // On ajoute la catégorie en file d'attente pour être enregistrer en BDD
            $manager->persist($category);
        }
    }

    private function createPost (ObjectManager $manager): void
    {
        for ($i=0; $i < 100; $i++) { 
            $post = new Post;
            $post->setTitle($this->faker->words(5, true))
                ->setDescription($this->faker->paragraphs(5, true))
                ->setCreatedAt($this->faker->dateTime());
            
            $manager->persist($post);
        }
    }
}
