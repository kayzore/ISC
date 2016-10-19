<?php
// src/ISC/PlatformBundle/Activite/ISCActivite.php

namespace ISC\PlatformBundle\Activite;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Entity\ActiviteImage;
use ISC\PlatformBundle\Entity\ActiviteLikes;
use ISC\PlatformBundle\Entity\UserNotifs;
use ISC\PlatformBundle\User\ISCUser;

class ISCActivite extends \Twig_Extension
{
    private $em;
    private $userService;
    private $container;

    /**
     * ISCActivite constructor.
     * @param EntityManager $em
     * @param ISCUser $userService
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ISCUser $userService, ContainerInterface $container)
    {
        $this->em           = $em;
        $this->userService  = $userService;
        $this->container    = $container;
    }

    /**
     * @param $idUser
     * @return array
     */
    public function getFriendsList($idUser)
    {
        $arrayFriendId = $this->em->getRepository("ISCPlatformBundle:UserFriend")->getFriendId($idUser);
        $i = 0;
        $arrayFriendIds = [];
        foreach ($arrayFriendId as $friendId) {
            $arrayFriendIds[$i] = $friendId->getFriend()->getId();
            $i++;
        }
        return $arrayFriendIds;
    }

    /**
     * @param $texte
     * @return mixed
     */
    public function checkSmiley($texte){
        $smileyArray = array(
            ' o_O',
            ' O_o',
            ' :)',
            ' :(',
            ' :P',
            ' :p',
            ' :D',
            ' :O',
            ' :o',
            ' ;)',
            ' :3'
        );
        $smileyImgArray = array(
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque2.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque2-inverse.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/happy1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/bad1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/langue1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/langue1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/big-happy1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/clin-doeil1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->container->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/mignon1.png" style="padding-bottom: 5px;">'
        );
        $texteFormat = str_replace($smileyArray, $smileyImgArray, $texte);

        return $texteFormat;
    }

    /**
     * @param $idUser
     * @param $arrayFriendId
     * @return array
     */
    public function getActivites($idUser, $arrayFriendId){
        $listActivites = $this->em->getRepository("ISCPlatformBundle:Activite")->getActivites($idUser, $arrayFriendId);
        return $listActivites;
    }

    /**
     * @param $idUser
     * @param $arrayFriendId
     * @return int
     */
    public function getNbTotalActivites($idUser, $arrayFriendId)
    {
        $nbActivite = $this->em->getRepository("ISCPlatformBundle:Activite")->getTotalActivite($idUser, $arrayFriendId);

        return count($nbActivite);
    }

    /**
     * @param $form
     * @param $request
     * @param $idUser
     * @param $activite
     * @return string
     */
    public function setActivite($form, $request, $idUser, $activite)
    {
        if($form->handleRequest($request)->isValid()){
            $userInformations = $this->em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $idUser));
            $activite->setUser($userInformations);
            $webPath = $this->container->get('kernel')->getRootDir().'/../web';
            $checkImage = false;
            $checkTexte = false;
            $resultSetActivite = array(false);
            $resultEditFile = false;
            if(is_object($activite->getImage()->getFile())){
                $fileName = md5(uniqid()).'.'.$activite->getImage()->getFile()->guessExtension();
                $file = $activite->getImage()->getFile();
                $image = new ActiviteImage();
                $image->setImageName($fileName);
                if($activite->getImage()->getEditFile() === true){
                    $image->setUrlImage('http://' . $this->container->get('request')->server->get('SERVER_NAME') . '/uploads/imgTmp/'.$fileName);
                    $file->move($webPath. '/uploads/imgTmp/', $fileName);
                    $resultEditFile = true;
                }
                else{
                    $image->setUrlImage('http://' . $this->container->get('request')->server->get('SERVER_NAME') . '/uploads/img/'.$fileName);
                    $file->move($webPath. '/uploads/img/', $fileName);
                }
                $activite->setImage($image);
                $checkImage =true;
            }
            if($form["textActivity"]->getData() != ""){
                $contentActu =	str_replace("<", " < ", $form["textActivity"]->getData());
                $activite->setTextActivity(nl2br($contentActu));
                $checkTexte = true;
            }
            if($checkImage === true || $checkTexte === true){
                $resultSetActivite[0] = true;
            }
            else{
                $resultSetActivite[0] = false;
            }
            if($resultEditFile === true){
                $activite->setApproved(false);
                array_push($resultSetActivite, true, $fileName);
            }
            else{
                $activite->setApproved(true);
                array_push($resultSetActivite, false);
            }
            array_push($resultSetActivite, $activite->getId());
            $activite->setDatetimeActivity(new \DateTime());
            $this->em->persist($activite);
            $this->em->flush();
            if($resultSetActivite[0] === false){
                return 'ErrorOneField';
            }
            elseif($resultSetActivite[1] === true){
                return 'RedirectEditFile';
            }
        }
    }

    /**
     * @param $actualite
     * @param $idActu
     * @param $userInformations
     * @param $idUser
     * @return string
     */
    public function getLikeZone($actualite, $idActu, $userInformations)
    {
        $listLikesActivite = $this->em->getRepository('ISCPlatformBundle:ActiviteLikes')->findBy(array('activite' => $actualite));
        $listActivites = $this->em->getRepository('ISCPlatformBundle:Activite')->findOneBy(array('id' => $idActu));

        $dateTimeActivite = $listActivites->getDatetimeActivity()->format('d-m-Y H:i:s');

        $classLikeActuSuccess = 'btn btn-success btn-xs clickable';
        $classLikeActuPrimary = 'btn btn-primary btn-xs clickable';
        $tooltip_like = '';
        $liked = false;
        foreach ($listLikesActivite as $likeActu) {
            if($likeActu->getUser()->getId() == $userInformations->getId()){
                $liked = true;
                $tooltip_like = $tooltip_like . 'Vous<br />';
            }
            else{
                $userLikeInformations = $this->em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $likeActu->getUser()->getId()));
                $tooltip_like = $tooltip_like . $userLikeInformations->getUsername(). '<br />';

            }
        }
        if($liked === true){
            $jsLikeActu = 'javascript:DelLike(this.getAttribute(\'id\'));';
            $classLikeActu = $classLikeActuSuccess;
            $textLikeActu = '<i class="fa fa-thumbs-up"></i> J<span style="text-transform:lowercase;">\'aime</span> <span id="loadLike'.$idActu.'" style="display:none;"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading</span></a>';

        }
        else{
            $jsLikeActu = 'javascript:AddLike(this.getAttribute(\'id\'));';
            $classLikeActu = $classLikeActuPrimary;
            $textLikeActu = '<i class="fa fa-thumbs-o-up"></i> J<span style="text-transform:lowercase;">\'aime</span> <span id="loadLike'.$idActu.'" style="display:none;"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading</span></a>';

        }
        $likeListUser = '<a data-toggle="tooltip" title="'.$tooltip_like.'" class="my_tooltip" style="text-transform:lowercase;color:black;text-decoration:none;">'.count($listLikesActivite).' <i class="fa fa-heart"></i></a>';
        $likeActuText = '<span class="pull-left"><a onclick="' . $jsLikeActu . '" id="'.$idActu.'" class="'.$classLikeActu.'">'.$textLikeActu.' | '.$likeListUser.'</span>';

        $likeZoneHtml = $likeActuText . ' <span class="pull-right"><i class="fa fa-calendar"></i> ' . $dateTimeActivite;
        return $likeZoneHtml;
    }

    /**
     * @param $idActu
     * @param $idUser
     * @return string
     */
    public function setLike($idActu, $idUser)
    {
        $listLikesNotifActivite = $this->em->getRepository("ISCPlatformBundle:UserNotifs")->findBy(array('activite' => $idActu, 'userFrom' => $idUser));
        $infoActivites = $this->em->getRepository("ISCPlatformBundle:Activite")->findOneBy(array('id' => $idActu));
        $userInformations = $this->em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $idUser));
        $likeActuQuery = new ActiviteLikes();
        $likeActuQuery->setActivite($infoActivites);
        $likeActuQuery->setUser($userInformations);
        $this->em->persist($likeActuQuery);
        if(count($listLikesNotifActivite) == 0) {
            if ($infoActivites->getUser()->getId() != $userInformations->getId()) {
                $likeNotif = new UserNotifs();
                $likeNotif->setUserFrom($userInformations);
                $likeNotif->setUserTo($infoActivites->getUser());
                $likeNotif->setType('Like');
                $likeNotif->setActivite($infoActivites);
                $likeNotif->setView(false);
                $likeNotif->setDatetimeNotif(new \DateTime());
                $this->em->persist($likeNotif);
            }
        }
        $this->em->flush();

        $likeZoneHtml = $this->getLikeZone($infoActivites, $idActu, $userInformations);

        return $likeZoneHtml;
    }

    /**
     * @param $idActu
     * @param $idUser
     * @return string
     */
    public function removeLike($idActu, $idUser)
    {
        $infoActivites = $this->em->getRepository("ISCPlatformBundle:Activite")->findOneBy(array('id' => $idActu));
        $userInformations = $this->em->getRepository("ISCUserBundle:User")->findOneBy(array('id' => $idUser));
        $likeActus = $this->em->getRepository('ISCPlatformBundle:ActiviteLikes')->findBy(array('activite' => $idActu, 'user' => $idUser));
        foreach ($likeActus as $likeActu) {
            $this->em->remove($likeActu);
        }
        $this->em->flush();
        $likeZoneHtml = $this->getLikeZone($infoActivites, $idActu, $userInformations);

        return $likeZoneHtml;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'rawPerso' => new \Twig_Filter_Method($this, 'getRawPerso',
                array('is_safe' => array('html'))
            ),
        );
    }

    /**
     * @param $texte
     * @return string
     */
    public function getRawPerso($texte)
    {
        return nl2br($texte);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ISCActivite';
    }
}