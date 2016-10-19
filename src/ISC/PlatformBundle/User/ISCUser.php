<?php
// src/ISC/PlatformBundle/User/ISCUser.php

namespace ISC\PlatformBundle\User;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use ISC\UserBundle\Entity\User;

class ISCUser
{
    private $em;
    private $serverUrl;
    private $router;

    public function __construct(EntityManager $em, $serverUrl, Router $router)
    {
        $this->em           = $em;
        $this->serverUrl    = $serverUrl;
        $this->router       = $router;
    }

    /**
     * @param $idUser
     */
    public function checkAvatar($idUser)
    {
        $userInformations = $this->em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $idUser));
        if ($userInformations->getUrlAvatar() == NULL) {
            $userInformations->setNameAvatar('no-avatar.png');
            $userInformations->setUrlAvatar($this->serverUrl . '/assets/images/no-avatar.png');
            $userInformations->setExtensionAvatar('.png');
            $this->em->persist($userInformations);
            $this->em->flush();
        }
    }

    /**
     * @param $idUser
     * @return array
     */
    public function getNotifications($idUser)
    {
        $userNotifs = $this->em->getRepository("ISCPlatformBundle:UserNotifs")->getUserNotifications($idUser);
        $listNotifsFormat = '';
        if(count($userNotifs) > 0){
            $idLastActuArray = array("0");
            foreach ($userNotifs as $notif) {
                $userMultiNotifs = $this->em->getRepository("ISCPlatformBundle:UserNotifs")->getIfMultiUserNotif($notif->getUserTo(), $notif->getActivite()->getId());
                if(!in_array($notif->getActivite()->getId(), $idLastActuArray)) {
                    $typeNotif = '';
                    $urlActuView = $this->router->generate('isc_platform_view_actu', array('idActu' => $notif->getActivite()->getId()));
                    if (count($userMultiNotifs) > 1) {
                        if ($notif->getType() == 'Like') {
                            $typeNotif = count($userMultiNotifs) . ' personnes ont aimés votre <a href="' . $urlActuView . '">actualité</a>';
                        }
                        if ($notif->getView() == 0) {
                            $listNotifsFormat .= '<a class="content"><div class="notification-item"><span class="label label-danger pull-right">New !</span><h4 class="item-title">' . $typeNotif . '</h4><p class="item-info">' . $notif->getDatetimeNotif()->format('d-m-Y H:i:s') . '</p></div></a>';
                        }
                        else {
                            $listNotifsFormat .= '<a class="content"><div class="notification-item"><h4 class="item-title">' . $typeNotif . '</h4><p class="item-info">' . $notif->getDatetimeNotif()->format('d-m-Y H:i:s') . '</p></div></a>';
                        }
                    }
                    else {
                        if ($notif->getType() == 'Like') {
                            $typeNotif = ' à aimé votre <a href="' . $urlActuView . '">actualité</a>';
                        }
                        if ($notif->getView() == 0) {
                            $listNotifsFormat .= '<a class="content"><div class="notification-item"><span class="label label-danger pull-right">New !</span><h4 class="item-title"><a href="/profil/' . $notif->getUserFrom()->getUsername() . '">' . $notif->getUserFrom()->getUsername() . '</a>' . $typeNotif . '</h4><p class="item-info">' . $notif->getDatetimeNotif()->format('d-m-Y H:i:s') . '</p></div></a>';
                        } else {
                            $listNotifsFormat .= '<a class="content"><div class="notification-item"><h4 class="item-title"><a href="/profil/' . $notif->getUserFrom()->getUsername() . '">' . $notif->getUserFrom()->getUsername() . '</a>' . $typeNotif . '</h4><p class="item-info">' . $notif->getDatetimeNotif()->format('d-m-Y H:i:s') . '</p></div></a>';
                        }
                    }
                    array_push($idLastActuArray, $notif->getActivite()->getId());
                }
            }
        }
        else{
            $listNotifsFormat .= '<div class="alert alert-info">Aucune notification</div>';
        }
        $userNbNewNotifs = $this->em->getRepository("ISCPlatformBundle:UserNotifs")->getNbMyNewNotif($idUser);

        $arrayUserNotifications = array($listNotifsFormat, count($userNbNewNotifs));

        return $arrayUserNotifications;
    }

    /**
     * @param $idUser
     * @param $idActu
     * @return array|\ISC\PlatformBundle\Entity\Activite[]|\ISC\PlatformBundle\Entity\UserNotifs[]|\ISC\UserBundle\Entity\User[]
     */
    public function getOneNotification($idUser, $idActu)
    {
        $notification = $this->em->getRepository("ISCPlatformBundle:UserNotifs")->findBy(array('userTo' => $idUser, 'activite' => $idActu));

        return $notification;
    }

    /**
     * @param $term
     * @return array|string
     */
    public function getUserBySearchTerm($term)
    {
        $userInformations = $this->em->getRepository("ISCUserBundle:User")->getSearchListUser($term);
        if(count($userInformations) > 0){
            foreach ($userInformations as $key => $value) {
                $data[] = array('id' => $value->getId(), 'username' => $value->getUsername());
            }
        }
        else {
            $data = array('id' => '', 'username' => $term);
        }
        $data = json_encode($data);

        return $data;
    }
}