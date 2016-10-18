<?php
// src/ISC/PlatformBundle/User/ISCUser.php

namespace ISC\PlatformBundle\User;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use ISC\UserBundle\Entity\User;

class ISCUser
{
    private $em;
    private $container;

    /**
     * ISCUser constructor.
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em           = $em;
        $this->container    = $container;
    }

    /**
     * @param $idUser
     */
    public function checkAvatar($idUser)
    {
        $userInformations = $this->em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $idUser));
        if ($userInformations->getUrlAvatar() == NULL) {
            $userInformations->setNameAvatar('no-avatar.png');
            $userInformations->setUrlAvatar('http://'.$this->container->get('request')->server->get('SERVER_NAME') . '/assets/images/no-avatar.png');
            $userInformations->setExtensionAvatar('.png');
            $this->em->persist($userInformations);
            $this->em->flush();
        }
    }
}