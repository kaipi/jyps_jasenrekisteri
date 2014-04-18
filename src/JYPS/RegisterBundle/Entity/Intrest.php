<?php

namespace JYPS\RegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Intrest implements UserInterface, \Serializable
{
/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
    * @ORM\Column(type="integer")
    */
    private $member_id;
    /**
    * @ORM\Column(type="integer")
    */
    private $intrest_id;

      /**
    * @ORM\ManyToOne(targetEntity="Member", inversedBy="intrests")
    * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
    */
     protected $intrest;
     /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
        ) = unserialize($serialized);
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
     * Set member_id
     *
     * @param integer $memberId
     * @return Intrest
     */
    public function setMemberId($memberId)
    {
        $this->member_id = $memberId;

        return $this;
    }

    /**
     * Get member_id
     *
     * @return integer 
     */
    public function getMemberId()
    {
        return $this->member_id;
    }

    /**
     * Set intrest_id
     *
     * @param integer $intrestId
     * @return Intrest
     */
    public function setIntrestId($intrestId)
    {
        $this->intrest_id = $intrestId;

        return $this;
    }

    /**
     * Get intrest_id
     *
     * @return integer 
     */
    public function getIntrestId()
    {
        return $this->intrest_id;
    }

    /**
     * Set intrest
     *
     * @param \JYPS\RegisterBundle\Entity\Member $intrest
     * @return Intrest
     */
    public function setIntrest(\JYPS\RegisterBundle\Entity\Member $intrest = null)
    {
        $this->intrest = $intrest;

        return $this;
    }

    /**
     * Get intrest
     *
     * @return \JYPS\RegisterBundle\Entity\Member 
     */
    public function getIntrest()
    {
        return $this->intrest;
    }
}
