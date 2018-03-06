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
    /**
     * @var string
     */
    private $expiredTime;

    public function __construct(RegistryInterface $registry, string $expiredTime)
    {
        $this->expiredTime = $expiredTime;

        parent::__construct($registry, Image::class);
    }

    public function removeExpiredImages(): int
    {
        return $this->getEntityManager()
            ->createQueryBuilder('i')
            ->delete($this->getClassName(), 'i')
            ->where('i.createdAt < :yesterday')
            ->setParameter('yesterday', (new \DateTime())->modify($this->expiredTime))
            ->getQuery()
            ->execute()
        ;
    }

    public function expiredImagesCount(): int
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i) as ic')
            ->where('i.createdAt < :yesterday')
            ->setParameter('yesterday', (new \DateTime())->modify($this->expiredTime))
            ->getQuery()
            ->getSingleScalarResult() ?? 0
        ;
    }

    public function findExpiredImages(): iterable
    {
        return $this->createQueryBuilder('i')
            ->where('i.createdAt < :yesterday')
            ->setParameter('yesterday', (new \DateTime())->modify($this->expiredTime))
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSumSize(): int
    {
        return $this->createQueryBuilder('i')
            ->select('SUM(i.size) as size')
            ->getQuery()
            ->getSingleScalarResult() ?? 0
        ;
    }
}
