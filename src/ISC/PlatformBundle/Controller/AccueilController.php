<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormError;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Form\Type\ActiviteType;
use ISC\PlatformBundle\Entity\UserNotifs;

class AccueilController extends Controller
{
    public function indexAction(Request $request)
    {
    	if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $userService = $this->get('isc_platform.user');
            var_dump('NULL');
            $activitesService = $this->get('isc_platform.activite');
            var_dump('NULL1');
            $userService->checkAvatar($user->getId());
            var_dump('NULL4');
            $activite = new Activite();
            $form = $this->get('form.factory')->create(new ActiviteType(), $activite);
            $resultSetNewActivite = $activitesService->setActivite($form, $request, $user->getId(), $activite);
            if($resultSetNewActivite[0] == 'RedirectEditFile'){
                return $this->redirectToRoute('isc_platform_homepage_pixie_actualite', array('filename' => $resultSetNewActivite[1], 'idActu' => $resultSetNewActivite[2]));
            }
            elseif($resultSetNewActivite == 'ErrorOneField'){
                $form->get('textActivity')->addError(new FormError('Vous devez au minimum ajouter du texte ou une image.'));
            }
            $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
            $userActivites = $activitesService->getActivites($user->getId());
            return $this->render('ISCPlatformBundle:Membres:index.html.twig', array(
                'form' 					=> $form->createView(),
                'userNotifications'		=> $userNotifications,
                'userActivites' 	    => $userActivites,
            ));
	    }
    	return $this->render('ISCPlatformBundle:Visiteurs:index.html.twig');
    }
}
