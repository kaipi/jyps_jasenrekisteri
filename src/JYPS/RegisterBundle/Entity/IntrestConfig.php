<?php

namespace JYPS\RegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class IntrestConfig implements UserInterface, \Serializable
{
	 /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
    */ 
    private $intrestname;
    /**
     * @ORM\Column(type="integer")
    */ 
    private $order;

    public function getUsername()
    {
        return $this->username;
    }
    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
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
     * Set intrestname
     *
     * @param string $intrestname
     * @return IntrestConfig
     */
    public function setIntrestname($intrestname)
    {
        $this->intrestname = $intrestname;

        return $this;
    }

    /**
     * Get intrestname
     *
     * @return string 
     */
    public function getIntrestname()
    {
        return $this->intrestname;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return IntrestConfig
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
