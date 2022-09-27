<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->createCategory($manager);
        // On exécute toutes els requêtes en attente puis on vide la file.
        $manager->flush();
    }

    private function createCategory (ObjectManager $manager): void
    {
        // On charge le générateur de Faker
        $faker = Factory::create();
        // On lance une boucle pour créer 10 catégories
        for ($i=0; $i < 10; $i++) { 
            // On instancie l'entité Category pour générer nos catégories
            $category = new Category();
            // On donne un nom à la catégorie généré par Faker
            $category->setName($faker->words(3, true));
            // On ajoute la catégorie en file d'attente pour être enregistrer en BDD
            $manager->persist($category);
        }
    }
}
