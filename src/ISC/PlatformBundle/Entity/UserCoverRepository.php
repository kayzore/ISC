<?php

namespace ISC\PlatformBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserCoverRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserCoverRepository extends EntityRepository
{
    /**
     * @param $idUser
     * @return array
     */
    public function getCover($idUser)
	{
		$qb = $this->createQueryBuilder('a');
		$qb
		    ->where('a.user = :idUser')
		    ->setParameter('idUser', $idUser)
     		->orderBy('a.positionCover', 'ASC');
		return $qb
			->getQuery()
		    ->getResult();
	}
}
