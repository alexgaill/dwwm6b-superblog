<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private array $users;

    public function __construct()
    {
        // On charge le générateur de Faker
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->users = $this->createUser($manager);
        $this->createCategory($manager);
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
            // On appelle la méthode createPost pour associer 10 articles à chaque catégorie
            // On passe la catégorie nouvellement créée aux articles.
            $this->createPost($manager, $category);
        }
    }

    private function createPost (ObjectManager $manager, Category $category): void
    {
        for ($i=0; $i < 10; $i++) { 
            $post = new Post;
            $post->setTitle($this->faker->words(5, true))
                ->setDescription($this->faker->paragraphs(5, true))
                ->setCreatedAt($this->faker->dateTime())
                // setCategory permet d'associer une catégorie à l'article
                ->setCategory($category)
                // On associe un utilisateur aléatoire provenant du tableau d'utilisateurs créés
                ->setUser($this->users[rand(0, count($this->users) -1)])
                ;
            
            $manager->persist($post);
        }
    }

    /**
     * Undocumented function
     *
     * @param ObjectManager $manager
     * @return array<User>
     */
    private function createUser(ObjectManager $manager): array
    {
        $users = array();
        for ($i=0; $i < 3; $i++) { 
            $user = new User;
            $user->setPrenom($this->faker->firstName())
                ->setNom($this->faker->lastName());
            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }
}
