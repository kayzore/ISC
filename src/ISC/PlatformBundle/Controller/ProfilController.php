<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Form\Type\ActiviteType;

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
}
