<?php
// src/ISC/PlatformBundle/Activite/ISCActivite.php

namespace ISC\PlatformBundle\Activite;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Entity\ActiviteImage;
use ISC\PlatformBundle\Entity\ActiviteLikes;
use ISC\PlatformBundle\Entity\UserNotifs;
use ISC\PlatformBundle\User\ISCUser;

class ISCActivite extends \Twig_Extension
{
    private $em;
    private $serverUrl;
    private $kernelRootDir;
    private $router;

    /**
     * ISCActivite constructor.
     * @param EntityManager $em
     * @param ISCUser $userService
     * @param $serverUrl
     * @param $kernelRootDir
     * @param Router $router
     */
    public function __construct(EntityManager $em, $serverUrl, $kernelRootDir, Router $router)
    {
        $this->em               = $em;
        $this->serverUrl        = $serverUrl;
        $this->kernelRootDir    = $kernelRootDir;
        $this->router           = $router;
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
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/choque2.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/choque2-inverse.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/happy1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/bad1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/langue1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/langue1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/big-happy1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/choque1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/choque1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/clin-doeil1.png" style="padding-bottom: 5px;">',
            ' <img src="'.$this->serverUrl.'/assets/images/smileyActu/mignon1.png" style="padding-bottom: 5px;">'
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
     * @return array|\ISC\PlatformBundle\Entity\Activite[]|\ISC\PlatformBundle\Entity\ActiviteLikes[]
     */
    public function getMyActivites($idUser){
        $listMyActivites = $this->em->getRepository("ISCPlatformBundle:Activite")->findBy(array('user' => $idUser), array('datetimeActivity' => 'DESC'));
        return $listMyActivites;
    }

    /**
     * @param $idLastActu
     * @param $idUser
     * @return string
     */
    public function getActivitesAfterId($idLastActu, $idUser)
    {
        $arrayFriendId = $this->getFriendsList($idUser);

        $listActivites = $this->em->getRepository("ISCPlatformBundle:Activite")->getActivitesAfterId($idUser, $arrayFriendId, $idLastActu);
        $activiteHtml = '';
        foreach ($listActivites as $activite) {
            if(count($activite->getLikes()) > 0){
                $classLikeActuSuccess = 'btn btn-success btn-xs clickable';
                $classLikeActuPrimary = 'btn btn-primary btn-xs clickable';
                $tooltip_like = '';
                $liked = false;
                foreach ($activite->getLikes() as $likeActu) {
                    if($likeActu->getUser()->getId() == $idUser){
                        $liked = true;
                        $tooltip_like = $tooltip_like . 'Vous<br />';
                    }
                    else{
                        $tooltip_like = $tooltip_like . $likeActu->getUser()->getUsername(). '<br />';
                    }
                }
                if($liked === true){
                    $jsLikeActu = 'javascript:DelLike(this.getAttribute(\'id\'));';
                    $classLikeActu = $classLikeActuSuccess;
                    $textLikeActu = '<i class="fa fa-thumbs-up"></i> J<span style="text-transform:lowercase;">\'aime</span> <span id="loadLike'.$activite->getId().'" style="display:none;"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading</span></a>';

                }
                else{
                    $jsLikeActu = 'javascript:AddLike(this.getAttribute(\'id\'));';
                    $classLikeActu = $classLikeActuPrimary;
                    $textLikeActu = '<i class="fa fa-thumbs-o-up"></i> J<span style="text-transform:lowercase;">\'aime</span> <span id="loadLike'.$activite->getId().'" style="display:none;"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading</span></a>';

                }
                $likeListUser = '<a data-toggle="tooltip" title="'.$tooltip_like.'" class="my_tooltip" style="text-transform:lowercase;color:black;text-decoration:none;">'.count($activite->getLikes()).' <i class="fa fa-heart"></i></a>';
                $likeActuHtml = '<span class="pull-left"><a onclick="' . $jsLikeActu . '" id="'.$activite->getId().'" class="'.$classLikeActu.'">'.$textLikeActu.' | '.$likeListUser.'</span>';
            }
            else{
                $likeActuHtml = '<span class="pull-left"><a onclick="javascript:AddLike(this.getAttribute(\'id\'));" id="'.$activite->getId().'" class="btn btn-primary btn-xs clickable"><i class="fa fa-thumbs-o-up"></i> J<span style="text-transform:lowercase;">\'aime</span> <span id="loadLike'.$activite->getId().'" style="display:none;"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading</span></a></span>';
            }
            if ($activite->getTextActivity() != NULL) {
                $textActuFormat = $this->checkSmiley($activite->getTextActivity());
                $contentActu = '<div class="col-md-12" style="box-shadow: 0px 0px 0px 1px rgba(0,0,0,0.1);display: inline-block;"><br /><p>' . $textActuFormat . '</p><img src="' . $activite->getImage()->getUrlImage() . '" style="max-width: 100%;max-height:300px;display: block;margin-left:auto;margin-right:auto;padding-bottom:10px;"></div>';
            }
            else {
                $contentActu = '<div class="col-md-12" style="box-shadow: 0px 0px 0px 1px rgba(0,0,0,0.1);display: inline-block;"><img src="' . $activite->getImage()->getUrlImage() . '" style="max-width: 100%;max-height:300px;display: block;margin-left:auto;margin-right:auto;padding-bottom:10px;"></div>';
            }
            $activiteHtml .= '<div class="row mod_actu" id="' . $activite->getId() . '" style="padding:20px;margin-bottom:20px;"><div class="col-md-12"><p><img src="' . $activite->getUser()->getUrlAvatar() . '" class="img-rounded img-responsive pull-left" style="max-height:70px;vertical-align:middle;"><strong><a href="' . $this->router->generate('isc_platform_profil_membres', array('username' => $activite->getUser()->getUsername())) . '">' . $activite->getUser()->getUsername() . '</a></strong></p></div><hr>' . $contentActu . '<div class="col-md-12 pull-left" style="padding-top:5px;" id="LikeZone' . $activite->getId() . '">' . $likeActuHtml . ' <span class="pull-right"><i class="fa fa-calendar"></i> ' . $activite->getDatetimeActivity()->format('d-m-Y H:i:s') . '</span></div></div>';
        }
        return $activiteHtml;
    }

    /**
     * @param $idUser
     * @param $idActu
     * @return string
     */
    public function getOneActivites($idActu){
        $listActivites = $this->em->getRepository("ISCPlatformBundle:Activite")->findOneBy(array('id' => $idActu));
        return $listActivites;
    }

    /**
     * @param $idActu
     * @return string
     */
    public function getOneActivitesForPublic($idActu){
        $activite = $this->em->getRepository("ISCPlatformBundle:Activite")->findOneBy(array('id' => $idActu));

        return $activite;
        if ($activite->getTextActivity() != NULL) {
            $textActuFormat = $this->checkSmiley($activite->getTextActivity());
            $contentActu = '<div class="col-md-12" style="box-shadow: 0px 0px 0px 1px rgba(0,0,0,0.1);display: inline-block;"><br /><p>' . $textActuFormat . '</p><img src="' . $activite->getImage()->getUrlImage() . '" style="max-width: 100%;max-height:300px;display: block;margin-left:auto;margin-right:auto;padding-bottom:10px;"></div>';
        }
        else {
            $contentActu = '<div class="col-md-12" style="box-shadow: 0px 0px 0px 1px rgba(0,0,0,0.1);display: inline-block;"><img src="' . $activite->getImage()->getUrlImage() . '" style="max-width: 100%;max-height:300px;display: block;margin-left:auto;margin-right:auto;padding-bottom:10px;"></div>';
        }
        $activiteHtml = '<div class="row mod_actu" id="' . $activite->getId() . '" style="padding:20px;margin-bottom:20px;"><div class="col-md-12"><p><img src="' . $activite->getUser()->getUrlAvatar() . '" class="img-rounded img-responsive pull-left" style="max-height:70px;vertical-align:middle;"><strong><a href="' . $this->router->generate('isc_platform_profil_membres', array('username' => $activite->getUser()->getUsername())) . '">' . $activite->getUser()->getUsername() . '</a></strong></p></div><hr>' . $contentActu . '<div class="col-md-12 pull-left" style="padding-top:5px;" id="LikeZone' . $activite->getId() . '"><span class="pull-right"><i class="fa fa-calendar"></i> ' . $activite->getDatetimeActivity()->format('d-m-Y H:i:s') . '</span></div></div>';

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
            $webPath = $this->kernelRootDir.'/../web';
            $checkImage = false;
            $checkTexte = false;
            $resultEditFile = false;
            $fileName = '';
            $wantEditFile = false;
            if($form["textActivity"]->getData() != ""){
                $contentActu =	str_replace("<", " < ", $form["textActivity"]->getData());
                $activite->setTextActivity(nl2br($contentActu));
                $checkTexte = true;
            }
            if(is_object($activite->getImage()->getFile())){
                $fileName = md5(uniqid()).'.'.$activite->getImage()->getFile()->guessExtension();
                $file = $activite->getImage()->getFile();
                $image = new ActiviteImage();
                $image->setImageName($fileName);
                if($activite->getImage()->getEditFile() === true){
                    $image->setUrlImage($this->serverUrl . 'uploads/imgTmp/'.$fileName);
                    $file->move($webPath. '/uploads/imgTmp/', $fileName);
                    $resultEditFile = true;
                }
                else{
                    $image->setUrlImage($this->serverUrl . 'uploads/img/'.$fileName);
                    $file->move($webPath. '/uploads/img/', $fileName);
                }
                $activite->setImage($image);
                $checkImage = true;
            }
            if($checkImage === true || $checkTexte === true){
                if($resultEditFile === true){
                    $activite->setApproved(false);
                    $wantEditFile = true;
                }
                else{
                    $activite->setApproved(true);
                }
                $activite->setDatetimeActivity(new \DateTime());
                $this->em->persist($activite);
                $this->em->flush();
                if($wantEditFile === true){
                    $resultNewActivite = array('RedirectEditFile', $fileName, $activite->getId());
                    return $resultNewActivite;
                }
            }
            else{
                return 'ErrorOneField';
            }
        }
    }

    /**
     * @param $idActu
     * @param $file
     * @return array
     */
    public function setEditImage($idActu, $file)
    {
        $actualite = $this->em->getRepository("ISCPlatformBundle:Activite")->findOneBy(array('id' => $idActu, 'approved' => false));
        $actualite->setApproved(true);
        $fileName = $actualite->getImage()->getImageName();
        $actualite->getImage()->setUrlImage($this->serverUrl.'uploads/img/'.$fileName);
        $path = $this->kernelRootDir.'/../web/uploads/img/'.$fileName;
        $file2 = file_get_contents($file);
        file_put_contents($path, $file2);
        $this->em->flush();

        return array('status' => "success","fileUploaded" => true);
    }

    /**
     * @param $filename
     * @return mixed
     */
    public function getUrlImage($filename)
    {
        $myImage = $this->em->getRepository("ISCPlatformBundle:ActiviteImage")->findOneBy(array('imageName' => $filename));
        $urlImage = $myImage->getUrlImage();
        return $urlImage;
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