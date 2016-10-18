<?php
// src/ISC/PlatformBundle/User/ISCUser.php

namespace ISC\PlatformBundle\User;

use Symfony\Component\HttpFoundation\Request;
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
    public function __construct(EntityManager $em, Request $request)
    {
        $this->em           = $em;
        $this->request = $request;
    }

    /**
     * @param $idUser
     */
    public function checkAvatar($idUser)
    {
        $userInformations = $this->em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $idUser));
        if ($userInformations->getUrlAvatar() == NULL) {
            $userInformations->setNameAvatar('no-avatar.png');
            $userInformations->setUrlAvatar('http://'.$this->request->get('request')->server->get('SERVER_NAME') . '/assets/images/no-avatar.png');
            $userInformations->setExtensionAvatar('.png');
            $this->em->persist($userInformations);
            $this->em->flush();
        }
    }
}