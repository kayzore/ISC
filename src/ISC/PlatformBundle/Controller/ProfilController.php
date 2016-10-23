<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Form\Type\ActiviteType;
use Symfony\Component\HttpFoundation\Response;

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

    public function showImageAction($username){
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $activitesService = $this->container->get('isc_platform.activite');
            $userInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('username' => $username));
            $userActivites = $activitesService->getMyImages($userInformation->getId());
            $userNbTotalActivites = $activitesService->getNbTotalMyActivites($userInformation->getId());
            $userNewInvitation = $em->getRepository("ISCPlatformBundle:UserFriend")->findBy(array('friend' => $userInformation->getId(), 'approvedFriend' => false));
            $myInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $user->getId()));
            return $this->render('ISCPlatformBundle:Profil:myProfilImage.html.twig', array(
                'userActivites'		    => $userActivites,
                'userInformation'		=> $userInformation,
                'userNewInvitation'		=> $userNewInvitation,
                'myInformation'		    => $myInformation,
                'userNbTotalActivites' 	=> $userNbTotalActivites,
            ));
        }
        return $this->redirectToRoute('isc_platform_homepage');
    }

    public function createActiviteAction(Request $request)
    {
        $user = $this->getUser();
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $activitesService = $this->container->get('isc_platform.activite');
            $activite = new Activite();
            $form = $this->get('form.factory')->create(new ActiviteType(), $activite);
            $resultSetNewActivite = $activitesService->setActivite($form, $request, $user->getId(), $activite);
            if(is_array($resultSetNewActivite) && $resultSetNewActivite[0] == 'RedirectEditFile'){
                return $this->redirectToRoute('isc_platform_homepage_pixie_actualite', array('filename' => $resultSetNewActivite[1], 'idActu' => $resultSetNewActivite[2]));
            }
            elseif($resultSetNewActivite === 'ErrorOneField'){
                $this->get('session')->getFlashBag()->add('errorAddActivite', 'Vous devez au minimum ajouter du texte ou une image.');
                return $this->redirect($this->generateUrl('isc_platform_profil_membres', array('username' => $user->getUsername())));
            }
        }
        return $this->redirectToRoute('isc_platform_profil_membres', array('username' => $user->getUsername()));
    }

    public function sendInvitToMembreAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $userService = $this->container->get('isc_platform.user');
            $user = $this->getUser();
            $idUserToSend = $request->query->get('id');
            $userService->sendInvitation($user->getId(), $idUserToSend);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->setContent('success');
            return $response;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('error');
        return $response;
    }

    public function acceptInvitOfMemberAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $userService = $this->container->get('isc_platform.user');
            $user = $this->getUser();
            $idUserToSend = $request->query->get('id');
            $userService->setAcceptInvitation($user->getId(), $idUserToSend);
            $listOfMyFriend = $userService->getListOfMyFriendHtml($user->getId());

            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->setContent($listOfMyFriend);
            return $response;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('error');
        return $response;
    }

    public function refuseInvitOfMemberAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $userService = $this->container->get('isc_platform.user');
            $user = $this->getUser();
            $idUserToSend = $request->query->get('id');
            $userService->setRefuseInvitation($user->getId(), $idUserToSend);
            $response = new Response();
            $response->headers->set('Content-Type', 'application/text');
            $response->setContent('success');
            return $response;
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('error');
        return $response;
    }

    public function editAvatarAction()
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $myInformation = $em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $user->getId()));
            return $this->render('ISCPlatformBundle:Profil:modifAvatar.html.twig', array(
                'myInformation' => $myInformation
            ));
        }
        return $this->redirectToRoute('isc_platform_homepage');
    }

    public function saveAvatarAction(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $userService = $this->container->get('isc_platform.user');
            $user = $this->getUser();
            $file = $request->request->get('imgData');
            $status = $userService->setEditAvatar($user->getId(), $file);
            return new JsonResponse($status);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/text');
        $response->setContent('error');
        return $response;
    }
}
