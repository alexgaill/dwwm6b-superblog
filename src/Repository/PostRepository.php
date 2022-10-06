<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Post[] Returns an array of Post objects
    */
   public function searchPost($value): array
   {
    // On charge le createur de requête QueryBuilder
        $qb = $this->createQueryBuilder('p');
        // On associe la table category à notre requête
        $qb->join('p.category', 'c')
            // On ajoute à notre requête des orWhere pour rechercher la value dans le title
            ->orWhere($qb->expr()->like('p.title', $qb->expr()->literal('%'.$value.'%')))
            // et dans la description du post
           ->orWhere($qb->expr()->like('p.description', $qb->expr()->literal('%'.$value.'%')))
           // Comme ce sont des termes imprécis que nous devons chercher, nous devons utiliser LIKE.
           // pour ça, on charge l'expression like ->expr()->like().
           // Afin d'ajouter la possibilité qu'il y ai du texte avant ou après le terme,
           // on indique que nous recherchons une expression literal afin d'utiliser les '%'
           ->orWhere($qb->expr()->like('c.name', $qb->expr()->literal('%'.$value.'%')))
           ;
        // On retourne notre requête qui sera exécutée et ses résultats récupérés.
        return $qb->getQuery()
                ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
