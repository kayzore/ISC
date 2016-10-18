<?php
// src/ISC/PlatformBundle/Accueil/ISCAccueil.php

namespace ISC\PlatformBundle\Accueil;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

class ISCAccueil
{
    private $em;
    private $container;

    /**
     * ISCAccueil constructor.
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em           = $em;
        $this->container    = $container;
    }
}