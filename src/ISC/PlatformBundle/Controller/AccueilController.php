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
            $activitesService = $this->container->get('isc_platform.activite');
            $this->container->get('isc_platform.user')->checkAvatar($user->getId());
            $activite = new Activite();
            $form = $this->get('form.factory')->create(new ActiviteType(), $activite);
            $resultSetNewActivite = $activitesService->setActivite($form, $request, $user->getId(), $activite);
            if(is_array($resultSetNewActivite) && $resultSetNewActivite[0] == 'RedirectEditFile'){
                return $this->redirectToRoute('isc_platform_homepage_pixie_actualite', array('filename' => $resultSetNewActivite[1], 'idActu' => $resultSetNewActivite[2]));
            }
            elseif($resultSetNewActivite === 'ErrorOneField'){
                $form->get('textActivity')->addError(new FormError('Vous devez au minimum ajouter du texte ou une image.'));
            }
            $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
            $arrayFriendId = $activitesService->getFriendsList($user->getId());
            $userActivites = $activitesService->getActivites($user->getId(), $arrayFriendId);
            $userNbTotalActivites = $activitesService->getNbTotalActivites($user->getId(), $arrayFriendId);
            return $this->render('ISCPlatformBundle:Membres:index.html.twig', array(
                'form' 					=> $form->createView(),
                'userNotifications'		=> $userNotifications,
                'userActivites' 	    => $userActivites,
                'userNbTotalActivites' 	=> $userNbTotalActivites,
            ));
	    }
    	return $this->render('ISCPlatformBundle:Visiteurs:index.html.twig');
    }

    public function loadMoreAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $user = $this->getUser();
            $activitesService = $this->container->get('isc_platform.activite');
            $userActivites = $activitesService->getActivitesAfterId($request->query->get('last'), $user->getId());
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            if($userActivites === null){
                $response->setContent("NULL");
                return $response;
            }
            $response->setContent($userActivites);
            return $response;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('success');
        return $response;
    }

    public function addLikeAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $user = $this->getUser();
            $idActu = $request->request->get('id');

            $activitesService = $this->container->get('isc_platform.activite');
            $result = $activitesService->setLike($idActu, $user->getId());

            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->setContent($result);
            return $response;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('error');
        return $response;
    }

    public function removeLikeAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $user = $this->getUser();
            $idActu = $request->request->get('id');

            $activitesService = $this->container->get('isc_platform.activite');
            $result = $activitesService->removeLike($idActu, $user->getId());

            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->setContent($result);
            return $response;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('success');
        return $response;
    }

    public function notifAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $userId = $request->query->get('id');
            $userService = $this->container->get('isc_platform.user');
            $userNotifications = $userService->getNotifications($userId);
            $data[] = array('content' => $userNotifications[0], 'number' => $userNotifications[1]);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }
        $response = new Response();
        $response->setContent('success');
        return $response;
    }

    public function viewActuAction($idActu)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $userService = $this->container->get('isc_platform.user');
            $activitesService = $this->container->get('isc_platform.activite');
            $user = $this->getUser();
            $notifConcernee = $userService->getOneNotification($user->getId(), $idActu);
            foreach ($notifConcernee as $notif) {
                $notif->setView(true);
            }
            $em->persist($notif);
            $em->flush();
            $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
            $userActivites = $activitesService->getOneActivites($idActu);
            return $this->render('ISCPlatformBundle:Membres:viewActu.html.twig', array(
                'userNotifications'		=> $userNotifications,
                'userActivites' 	    => $userActivites,
            ));
        }
        return $this->redirectToRoute('isc_platform_homepage');
    }

    public function searchAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $motcle = $request->request->get('term');
            $userService = $this->container->get('isc_platform.user');
            $data = $userService->getUserBySearchTerm($motcle);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($data);
            return $response;
        }
        $response = new Response();
        $response->setContent('success');
        return $response;
    }

    public function editImageActuAction($filename, $idActu)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $activitesService = $this->container->get('isc_platform.activite');
            $urlImage = $activitesService->getUrlImage($filename);
            $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
            return $this->render('ISCPlatformBundle:Membres:modifImageActu.html.twig', array(
                'urlImage' 				=> $urlImage,
                'idActu' 				=> $idActu,
                'userNotifications'		=> $userNotifications,
            ));
        }
        return $this->redirectToRoute('isc_platform_homepage');
    }

    public function saveImageActiviteAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $file = $request->request->get('imgData');
            $idActu = $request->request->get('idActu');
            $activitesService = $this->container->get('isc_platform.activite');
            $status = $activitesService->setEditImage($idActu, $file);
            return new JsonResponse($status);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('success');
        return $response;
    }
}
