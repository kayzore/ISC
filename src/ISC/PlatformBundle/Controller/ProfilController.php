<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProfilController extends Controller
{
    public function indexAction($username)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $activitesService = $this->container->get('isc_platform.activite');
            $userInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('username' => $username));
            $userActivites = $activitesService->getMyActivites($userInformation->getId());
            $userNbTotalActivites = $activitesService->getNbTotalMyActivites($userInformation->getId());
            $userNewInvitation = $em->getRepository("ISCPlatformBundle:UserFriend")->findBy(array('friend' => $userInformation->getId(), 'approvedFriend' => false));
            $myInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $user->getId()));
            return $this->render('ISCPlatformBundle:Profil:index.html.twig', array(
                'userActivites'		    => $userActivites,
                'userInformation'		=> $userInformation,
                'userNewInvitation'		=> $userNewInvitation,
                'myInformation'		    => $myInformation,
                'userNbTotalActivites' 	=> $userNbTotalActivites,
            ));
        }
        return $this->redirectToRoute('isc_platform_homepage');
    }
}
