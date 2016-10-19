<?php

namespace ISC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use \ISC\UserBundle\Entity\User;

/**
 * UserNotifs
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISC\PlatformBundle\Entity\UserNotifsRepository")
 */
class UserNotifs
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ISC\UserBundle\Entity\User", inversedBy="notificationsFrom")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity="ISC\UserBundle\Entity\User", inversedBy="notificationsTo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTo;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity="ISC\PlatformBundle\Entity\Activite")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activite;

    /**
     * @var boolean
     *
     * @ORM\Column(name="view", type="boolean")
     */
    private $view;

    /**
     * @ORM\Column(name="datetimeNotif", type="datetime")
     * @Assert\DateTime()
     */
    private $datetimeNotif;



    public function __construct()
    {
        $this->date = new \Datetime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return UserNotifs
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set view
     *
     * @param boolean $view
     * @return UserNotifs
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return boolean 
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set datetimeNotif
     *
     * @param \DateTime $datetimeNotif
     * @return UserNotifs
     */
    public function setDatetimeNotif($datetimeNotif)
    {
        $this->datetimeNotif = $datetimeNotif;

        return $this;
    }

    /**
     * Get datetimeNotif
     *
     * @return \DateTime
     */
    public function getDatetimeNotif()
    {
        return $this->datetimeNotif;
    }

    /**
     * Set userFrom
     *
     * @param \ISC\UserBundle\Entity\User $userFrom
     * @return UserNotifs
     */
    public function setUserFrom(User $userFrom)
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    /**
     * Get userFrom
     *
     * @return \ISC\UserBundle\Entity\User 
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * Set userTo
     *
     * @param \ISC\UserBundle\Entity\User $userTo
     * @return UserNotifs
     */
    public function setUserTo(User $userTo = null)
    {
        $this->userTo = $userTo;

        return $this;
    }

    /**
     * Get userTo
     *
     * @return \ISC\UserBundle\Entity\User 
     */
    public function getUserTo()
    {
        return $this->userTo;
    }

    /**
     * Set activite
     *
     * @param \ISC\PlatformBundle\Entity\Activite $activite
     * @return UserNotifs
     */
    public function setActivite(Activite $activite)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return \ISC\PlatformBundle\Entity\Activite 
     */
    public function getActivite()
    {
        return $this->activite;
    }
}
