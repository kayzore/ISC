<?php

namespace ISC\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \ISC\UserBundle\Entity\User;

/**
 * UserFriend
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISC\PlatformBundle\Entity\UserFriendRepository")
 */
class UserFriend
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
     * @ORM\ManyToOne(targetEntity="ISC\UserBundle\Entity\User", inversedBy="friends")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="ISC\UserBundle\Entity\User")
     */
    private $friend;

    /**
     * @var boolean
     *
     * @ORM\Column(name="approvedFriend", type="boolean")
     */
    private $approvedFriend;

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
     * Set approvedFriend
     *
     * @param boolean $approvedFriend
     * @return UserFriend
     */
    public function setApprovedFriend($approvedFriend)
    {
        $this->approvedFriend = $approvedFriend;

        return $this;
    }

    /**
     * Get approvedFriend
     *
     * @return boolean 
     */
    public function getApprovedFriend()
    {
        return $this->approvedFriend;
    }

    /**
     * Set user
     *
     * @param \ISC\UserBundle\Entity\User $user
     * @return UserFriend
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

    /**
     * Set friend
     *
     * @param \ISC\UserBundle\Entity\User $friend
     * @return UserFriend
     */
    public function setFriend(User $friend = null)
    {
        $this->friend = $friend;

        return $this;
    }

    /**
     * Get friend
     *
     * @return \ISC\UserBundle\Entity\User 
     */
    public function getFriend()
    {
        return $this->friend;
    }
}
