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
            if($user->getUsername() == $username){
                $activitesService = $this->container->get('isc_platform.activite');
                $activite = new Activite();
                $userActiviteForm = $this->get('form.factory')->create(new ActiviteType(), $activite);
                // TODO : Creer le form pour l avatar
                $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
                $userActivites = $activitesService->getMyActivites($user->getId());
                return $this->render('ISCPlatformBundle:Profil/MyProfil:index.html.twig', array(
                    'userActiviteForm' 		=> $userActiviteForm->createView(),
                    'userNotifications'		=> $userNotifications,
                    'userActivites'		    => $userActivites,
                ));
            }
            else{

            }
        }
        return $this->redirectToRoute('isc_platform_homepage');
    }
}
