<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Form\Type\ActiviteType;
use ISC\PlatformBundle\Entity\UserNotifs;

class ProfilController extends Controller
{
    public function indexAction($username)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $activitesService = $this->container->get('isc_platform.activite');
            $activite = new Activite();
            $userActiviteForm = $this->get('form.factory')->create(new ActiviteType(), $activite);
            // TODO : Creer le form pour l avatar
            $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
            $userInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('username' => $username));
            $userActivites = $activitesService->getMyActivites($userInformation->getId());
            $userNewInvitation = $em->getRepository("ISCPlatformBundle:UserFriend")->findBy(array('friend' => $userInformation->getId(), 'approvedFriend' => false));
            return $this->render('ISCPlatformBundle:Profil:index.html.twig', array(
                'form' 		            => $userActiviteForm->createView(),
                'userNotifications'		=> $userNotifications,
                'userActivites'		    => $userActivites,
                'userInformation'		=> $userInformation,
                'userNewInvitation'		=> $userNewInvitation,
            ));
        }
        return $this->redirectToRoute('isc_platform_homepage');
    }
}
