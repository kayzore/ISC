<?php
// src/ISC/PlatformBundle/Activite/ISCActivite.php

namespace ISC\PlatformBundle\Activite;

use Symfony\Component\DependencyInjection\ContainerInterface;
use ISC\PlatformBundle\Entity\Activite;
use Doctrine\ORM\EntityManager;

class ISCActivite
{
    private $em;
    private $userService;
    private $container;

    /**
     * ISCActivite constructor.
     * @param EntityManager $em
     * @param ISCUser $userService
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ISCUser $userService, ContainerInterface $container)
    {
        $this->em           = $em;
        $this->userService  = $userService;
        $this->container   = $container;
    }

    /**
     * @param $idUser
     * @return array
     */
    public function getFriendsList($idUser)
    {
        $arrayFriendId = $this->em->getRepository("ISCPlatformBundle:UserFriend")->getFriendId($idUser);
        return $arrayFriendId;
    }

    public function getActivites($idUser){
        $arrayFriendId = $this->getFriendsList($idUser);
        $listActivites = $this->em->getRepository("ISCPlatformBundle:Activite")->getActivites($idUser, $arrayFriendId);
        return $listActivites;
    }
}