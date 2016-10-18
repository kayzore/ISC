<?php

namespace ISC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \ISC\UserBundle\Entity\User;

/**
 * UserCover
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISC\PlatformBundle\Entity\UserCoverRepository")
 */
class UserCover
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
     * @ORM\ManyToOne(targetEntity="ISC\UserBundle\Entity\User", inversedBy="covers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var text
     *
     * @ORM\Column(name="urlCover", type="text")
     */
    private $urlCover;

    /**
     * @var integer
     *
     * @ORM\Column(name="positionCover", type="integer")
     */
    private $positionCover;



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
     * Set urlCover
     *
     * @param text $urlCover
     * @return UserCover
     */
    public function setUrlCover($urlCover)
    {
        $this->urlCover = $urlCover;

        return $this;
    }

    /**
     * Get urlCover
     *
     * @return text 
     */
    public function getUrlCover()
    {
        return $this->urlCover;
    }

    /**
     * Set positionCover
     *
     * @param integer $positionCover
     * @return UserCover
     */
    public function setPositionCover($positionCover)
    {
        $this->positionCover = $positionCover;

        return $this;
    }

    /**
     * Get positionCover
     *
     * @return integer 
     */
    public function getPositionCover()
    {
        return $this->positionCover;
    }

    /**
     * Set user
     *
     * @param \ISC\UserBundle\Entity\User $user
     * @return UserCover
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
