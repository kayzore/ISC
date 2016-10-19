<?php
// src/ISC/UserBundle/Entity/User.php

namespace ISC\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use \ISC\PlatformBundle\Entity\Activite;
use \ISC\PlatformBundle\Entity\UserCover;
use \ISC\PlatformBundle\Entity\UserFriend;
use \ISC\PlatformBundle\Entity\UserNotifs;
use \ISC\PlatformBundle\Entity\ActiviteLikes;

/**
 * @ORM\Entity(repositoryClass="ISC\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
   /**
   	* @ORM\Column(name="id", type="integer")
   	* @ORM\Id
   	* @ORM\GeneratedValue(strategy="AUTO")
   	*/
  	protected $id;

    /**
     * @Gedmo\Slug(fields={"username"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="urlAvatar", type="text", nullable=true)
     */
    private $urlAvatar = NULL;

    /**
     * @var string
     *
     * @ORM\Column(name="nameAvatar", type="text", nullable=true)
     */
    private $nameAvatar = NULL;

    /**
     * @var string
     *
     * @ORM\Column(name="extensionAvatar", type="text", nullable=true)
     */
    private $extensionAvatar = NULL;

    /**
     * @ORM\OneToMany(targetEntity="ISC\PlatformBundle\Entity\Activite", mappedBy="user")
     */
    private $activites;

    /**
     * @ORM\OneToMany(targetEntity="ISC\PlatformBundle\Entity\UserCover", mappedBy="user")
     */
    private $covers;

    /**
     * @ORM\OneToMany(targetEntity="ISC\PlatformBundle\Entity\UserFriend", mappedBy="user")
     */
    private $friends;

    /**
     * @ORM\OneToMany(targetEntity="ISC\PlatformBundle\Entity\UserNotifs", mappedBy="userFrom")
     */
    private $notificationsFrom;

    /**
     * @ORM\OneToMany(targetEntity="ISC\PlatformBundle\Entity\UserNotifs", mappedBy="userTo")
     */
    private $notificationsTo;

    /**
     * @ORM\OneToMany(targetEntity="ISC\PlatformBundle\Entity\ActiviteLikes", mappedBy="user")
     */
    private $likes;

    private $file;

    private $editFile;



    public function __construct()
    {
        $this->activites = new ArrayCollection();
        $this->covers = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        parent::__construct();
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return User
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set urlAvatar
     *
     * @param string $urlAvatar
     * @return User
     */
    public function setUrlAvatar($urlAvatar = NULL)
    {
        $this->urlAvatar = $urlAvatar;

        return $this;
    }

    /**
     * Get urlAvatar
     *
     * @return string 
     */
    public function getUrlAvatar()
    {
        return $this->urlAvatar;
    }

    /**
     * Set nameAvatar
     *
     * @param string $nameAvatar
     * @return User
     */
    public function setNameAvatar($nameAvatar = NULL)
    {
        $this->nameAvatar = $nameAvatar;

        return $this;
    }

    /**
     * Get nameAvatar
     *
     * @return string 
     */
    public function getNameAvatar()
    {
        return $this->nameAvatar;
    }

    /**
     * Set extensionAvatar
     *
     * @param string $extensionAvatar
     * @return User
     */
    public function setExtensionAvatar($extensionAvatar = NULL)
    {
        $this->extensionAvatar = $extensionAvatar;

        return $this;
    }

    /**
     * Get extensionAvatar
     *
     * @return string 
     */
    public function getExtensionAvatar()
    {
        return $this->extensionAvatar;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function upload()
    {
        // On récupère le nom original du fichier de l'internaute
        $name = md5(uniqid()).'.'.$this->$file->guessExtension();

        // On déplace le fichier envoyé dans le répertoire de notre choix
        $this->file->move($this->getUploadRootDir(), $name);

        // On sauvegarde le nom de fichier dans notre attribut $url
        $this->url = $name;

        return $name;
    }

    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/img';
    }

    protected function getUploadRootDir()
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return $this->get('kernel')->getRootDir().'/../web/uploads/Avatar/'.$this->getUploadDir();
    }

    /**
     * Set editFile
     *
     * @param boolean $editFile
     * @return User
     */
    public function setEditFile($editFile)
    {
        $this->editFile = $editFile;

        return $this;
    }

    /**
     * Get editFile
     *
     * @return boolean
     */
    public function getEditFile()
    {
        return $this->editFile;
    }

    /**
     * Add activites
     *
     * @param \ISC\PlatformBundle\Entity\Activite $activite
     * @return User
     */
    public function addActivite(Activite $activite)
    {
        $this->activites[] = $activite;
        $activite->setUser($this);
        return $this;
    }

    /**
     * Remove activites
     *
     * @param \ISC\PlatformBundle\Entity\Activite $activites
     */
    public function removeActivite(Activite $activites)
    {
        $this->activites->removeElement($activites);
    }

    /**
     * Get activites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivites()
    {
        return $this->activites;
    }

    /**
     * Add covers
     *
     * @param \ISC\PlatformBundle\Entity\UserCover $cover
     * @return User
     */
    public function addCover(UserCover $cover)
    {
        $this->covers[] = $cover;
        $cover->setUser($this);
        return $this;
    }

    /**
     * Remove covers
     *
     * @param \ISC\PlatformBundle\Entity\UserCover $covers
     */
    public function removeCover(UserCover $covers)
    {
        $this->covers->removeElement($covers);
    }

    /**
     * Get covers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCovers()
    {
        return $this->covers;
    }

    /**
     * Add friends
     *
     * @param \ISC\PlatformBundle\Entity\UserFriend $friend
     * @return User
     */
    public function addFriend(UserFriend $friend)
    {
        $this->friends[] = $friend;
        $friend->setUser($this);
        return $this;
    }

    /**
     * Remove friends
     *
     * @param \ISC\PlatformBundle\Entity\UserFriend $friends
     */
    public function removeFriend(UserFriend $friends)
    {
        $this->friends->removeElement($friends);
    }

    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * Add notificationsFrom
     *
     * @param \ISC\PlatformBundle\Entity\UserNotifs $notificationsFrom
     * @return User
     */
    public function addNotificationsFrom(UserNotifs $notificationsFrom)
    {
        $this->notificationsFrom[] = $notificationsFrom;
        $notificationsFrom->setUser($this);
        return $this;
    }

    /**
     * Remove notificationsFrom
     *
     * @param \ISC\PlatformBundle\Entity\UserNotifs $notificationsFrom
     */
    public function removeNotificationsFrom(UserNotifs $notificationsFrom)
    {
        $this->notificationsFrom->removeElement($notificationsFrom);
    }

    /**
     * Get notificationsFrom
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificationsFrom()
    {
        return $this->notificationsFrom;
    }

    /**
     * Add likes
     *
     * @param \ISC\PlatformBundle\Entity\ActiviteLikes $likes
     * @return User
     */
    public function addLike(ActiviteLikes $likes)
    {
        $this->likes[] = $likes;

        return $this;
    }

    /**
     * Remove likes
     *
     * @param \ISC\PlatformBundle\Entity\ActiviteLikes $likes
     */
    public function removeLike(ActiviteLikes $likes)
    {
        $this->likes->removeElement($likes);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add notificationsTo
     *
     * @param \ISC\PlatformBundle\Entity\UserNotifs $notificationsTo
     * @return User
     */
    public function addNotificationsTo(UserNotifs $notificationsTo)
    {
        $this->notificationsTo[] = $notificationsTo;
        $notificationsTo->setUser($this);
        return $this;
    }

    /**
     * Remove notificationsTo
     *
     * @param \ISC\PlatformBundle\Entity\UserNotifs $notificationsTo
     */
    public function removeNotificationsTo(UserNotifs $notificationsTo)
    {
        $this->notificationsTo->removeElement($notificationsTo);
    }

    /**
     * Get notificationsTo
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificationsTo()
    {
        return $this->notificationsTo;
    }
}
