<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormError;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Entity\ActiviteImage;
use ISC\PlatformBundle\Form\Type\ActiviteType;
use ISC\PlatformBundle\Entity\UserFriend;
use ISC\PlatformBundle\Entity\ActiviteLikes;
use ISC\PlatformBundle\Entity\UserAvatar;
use ISC\PlatformBundle\Entity\UserNotifs;

class AccueilController extends Controller
{
    public function indexAction(Request $request)
    {
    	if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $accueilService = $this->container->get('isc_platform.accueil');
	    }
    	return $this->render('ISCPlatformBundle:Visiteurs:index.html.twig');
    }
}
