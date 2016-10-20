<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ISC\UserBundle\Entity\User;

class RenderProfilController extends Controller
{
    public function NbActuAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $activitesService = $this->container->get('isc_platform.activite');
        $userInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('username' => $username));
        $userNbTotalActivites = $activitesService->getNbTotalMyActivites($userInformation->getId());
        return $this->render('ISCPlatformBundle:Profil:header.html.twig', array(
            'userNbTotalActivites' 	=> $userNbTotalActivites,
        ));
    }
}