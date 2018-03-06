<?php

/*
 * This file is part of MarkdownEditor.
 *
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Antal Áron <antalaron@antalaron.hu>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function removeExpiredImages()
    {
        return $this->getEntityManager()
            ->createQueryBuilder('i')
            ->delete($this->getClassName(), 'i')
            ->where('i.createdAt < :yesterday')
            ->setParameter('yesterday', (new \DateTime())->modify('-1 day'))
            ->getQuery()
            ->excute()
        ;
    }

    public function expiredImagesCount()
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i) as ic')
            ->where('i.createdAt < :yesterday')
            ->setParameter('yesterday', (new \DateTime())->modify('-1 day'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getSumSize(): int
    {
        return $this->createQueryBuilder('i')
            ->select('SUM(i.size) as size')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
