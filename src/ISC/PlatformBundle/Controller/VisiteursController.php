<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VisiteursController extends Controller
{
    public function viewImagePublicAction($idActu)
    {
        $activitesService = $this->container->get('isc_platform.activite');
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $userActivites = $activitesService->getOneActivites($idActu);
            return $this->render('ISCPlatformBundle:Membres:viewActu.html.twig', array(
                'userActivites' 	    => $userActivites,
            ));
        }
        else{
            $userActivites = $activitesService->getOneActivitesForPublic($idActu);
            return $this->render('ISCPlatformBundle:Visiteurs:viewActu.html.twig', array(
                'userActivites' 	=> $userActivites,
            ));
        }
    }
}