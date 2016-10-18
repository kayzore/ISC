<?php

namespace ISC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \ISC\UserBundle\Entity\User;
use \ISC\PlatformBundle\Entity\Activite;

/**
 * ActiviteLikes
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISC\PlatformBundle\Entity\ActiviteLikesRepository")
 */
class ActiviteLikes
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
     * @ORM\ManyToOne(targetEntity="ISC\PlatformBundle\Entity\Activite", inversedBy="likes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activite;

    /**
     * @ORM\ManyToOne(targetEntity="ISC\UserBundle\Entity\User", inversedBy="likes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;



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
     * Set activite
     *
     * @param \ISC\PlatformBundle\Entity\Activite $activite
     * @return ActiviteLikes
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

    /**
     * Set user
     *
     * @param \ISC\UserBundle\Entity\User $user
     * @return ActiviteLikes
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
