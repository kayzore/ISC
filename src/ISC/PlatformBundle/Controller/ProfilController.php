<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Form\Type\ActiviteType;
use ISC\UserBundle\Entity\User;
use ISC\UserBundle\Form\Type\UserType;
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
            $userEntity = new User();
            $userAvatarForm = $this->get('form.factory')->create(new UserType(), $userEntity);
            $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
            $userInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('username' => $username));
            $userActivites = $activitesService->getMyActivites($userInformation->getId());
            $userNbTotalActivites = $activitesService->getNbTotalMyActivites($userInformation->getId());
            $userNewInvitation = $em->getRepository("ISCPlatformBundle:UserFriend")->findBy(array('friend' => $userInformation->getId(), 'approvedFriend' => false));
            $myInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $user->getId()));
            return $this->render('ISCPlatformBundle:Profil:index.html.twig', array(
                'form' 		            => $userActiviteForm->createView(),
                'userAvatarForm'        => $userAvatarForm->createView(),
                'userNotifications'		=> $userNotifications,
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
