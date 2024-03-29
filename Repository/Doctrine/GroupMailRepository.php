<?php

namespace SQLI\EzToolboxBundle\Repository\Doctrine;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SQLI\EzToolboxBundle\Entity\Doctrine\GroupMail;

class GroupMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupMail::class);
    }

    public function deleteGroupMails()
    {
        return $this->createQueryBuilder('m')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
