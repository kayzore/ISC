<?php

namespace ISC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use \ISC\UserBundle\Entity\User;

/**
 * Activite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISC\PlatformBundle\Entity\ActiviteRepository")
 */
class Activite
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ISC\UserBundle\Entity\User", inversedBy="activites")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="ISC\PlatformBundle\Entity\ActiviteImage", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\Column(name="textActivity", type="text", nullable=true)
     */
    private $textActivity = null;

    /**
     * @ORM\Column(name="datetimeActivity", type="datetime")
     * @Assert\DateTime()
     */
    private $datetimeActivity;

    /**
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;



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
     * Set textActivity
     *
     * @param string $textActivity
     * @return Activite
     */
    public function setTextActivity($textActivity = null)
    {
        $this->textActivity = $textActivity;

        return $this;
    }

    /**
     * Get textActivity
     *
     * @return string 
     */
    public function getTextActivity()
    {
        return $this->textActivity;
    }

    /**
     * Set datetimeActivity
     *
     * @param \DateTime $datetimeActivity
     * @return Activite
     */
    public function setDatetimeActivity($datetimeActivity)
    {
        $this->datetimeActivity = $datetimeActivity;

        return $this;
    }

    /**
     * Get datetimeActivity
     *
     * @return \DateTime 
     */
    public function getDatetimeActivity()
    {
        return $this->datetimeActivity;
    }

    /**
     * Set image
     *
     * @param \ISC\PlatformBundle\Entity\ActiviteImage $image
     * @return Activite
     */
    public function setImage(ActiviteImage $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \ISC\PlatformBundle\Entity\ActiviteImage 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     * @return Activite
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean 
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set user
     *
     * @param \ISC\UserBundle\Entity\User $user
     * @return Activite
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ISC\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
