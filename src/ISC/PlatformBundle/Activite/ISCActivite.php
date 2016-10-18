<?php
// src/ISC/PlatformBundle/Activite/ISCActivite.php

namespace ISC\PlatformBundle\Activite;

use Doctrine\ORM\EntityManager;
use ISC\PlatformBundle\Entity\Activite;
use ISC\PlatformBundle\Entity\ActiviteImage;
use ISC\PlatformBundle\User\ISCUser;
use Symfony\Component\HttpFoundation\Request;

class ISCActivite extends \Twig_Extension
{
    private $em;
    private $userService;
    protected $request;

    public function __construct(EntityManager $em, ISCUser $userService, Request $request)
    {
        $this->em           = $em;
        $this->userService  = $userService;
        $this->request = $request;
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
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque2.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque2-inverse.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/happy1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/bad1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/langue1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/langue1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/big-happy1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/choque1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/clin-doeil1.png" style="padding-bottom: 5px;">',
            ' <img src="http://'.$this->request->get('request')->server->get('SERVER_NAME').'/assets/images/smileyActu/mignon1.png" style="padding-bottom: 5px;">'
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
            $webPath = $this->request->get('kernel')->getRootDir().'/../web';
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
                    $image->setUrlImage('http://' . $this->request->get('request')->server->get('SERVER_NAME') . '/uploads/imgTmp/'.$fileName);
                    $file->move($webPath. '/uploads/imgTmp/', $fileName);
                    $resultEditFile = true;
                }
                else{
                    $image->setUrlImage('http://' . $this->request->get('request')->server->get('SERVER_NAME') . '/uploads/img/'.$fileName);
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

    public function getFilters()
    {
        return array(
            'rawPerso' => new \Twig_Filter_Method($this, 'getRawPerso',
                array('is_safe' => array('html'))
            ),
        );
    }
    public function getRawPerso($texte)
    {
        return nl2br($texte);
    }

    public function getName()
    {
        return 'ISCActivite';
    }
}