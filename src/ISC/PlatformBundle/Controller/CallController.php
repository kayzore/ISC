<?php

namespace ISC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Form\Type\ActiviteType;
use ISC\UserBundle\Entity\User;
use ISC\UserBundle\Form\Type\UserType;

class CallController extends Controller
{
    public function getNotificationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $userNotifications = $em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($user->getId());
        return $this->render('ISCPlatformBundle:Notifications:getNotifications.html.twig', array(
            'userNotifications'	=> $userNotifications,
        ));
    }

    public function getFormAddActuAction()
    {
        $activite = new Activite();
        $userActiviteForm = $this->get('form.factory')->create(new ActiviteType(), $activite);
        return $this->render('ISCPlatformBundle::formAddActu.html.twig', array(
            'form' => $userActiviteForm->createView(),
        ));
    }
    public function getFormChangeAvatarAction()
    {
        $userEntity = new User();
        $userAvatarForm = $this->get('form.factory')->create(new UserType(), $userEntity);
        return $this->render('ISCPlatformBundle::formChangeAvatar.html.twig', array(
            'userAvatarForm' => $userAvatarForm->createView(),
        ));
    }
}