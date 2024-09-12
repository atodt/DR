<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private LoggerInterface $logger)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product returns a Product object with the given ID or NULL
     */
    public function findById(int $value): ?Product
    {
        $product = $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$product instanceof Product) {
            return null;
        }

        return $product;
    }

    public function createProduct(Product $product): Product
    {
        $this->logger->info('Creating new product');
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();

        return $product;
    }

    public function removeProduct(Product $product): void
    {
        $this->logger->info('Deleting product '.$product->getId());
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
    }

    public function saveProduct(Product $product): Product
    {
        $this->logger->info('Deleting product '.$product->getId());
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();

        return $product;
    }
}
