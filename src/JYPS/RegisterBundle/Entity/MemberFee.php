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
class MemberFee implements UserInterface, \Serializable
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
    private $fee_period;
    /**
    * @ORM\Column(type="decimal")
    */
    private $fee_amount_with_vat;
    /**
    * @ORM\Column(type="integer")
    */
    private $member_id;
     /**
    * @ORM\Column(type="string")
    */
    private $reference_number;
     /**
    * @ORM\Column(type="date")
    */
    private $due_date;
    /**
    * @ORM\Column(type="boolean")
    */
    private $paid;
    /**
    * @ORM\Column(type="string", nullable=true)
    */
    private $memo;
 
    /**
    * @ORM\ManyToOne(targetEntity="Member", inversedBy="memberfees")
    * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
    */
    protected $memberfee;
      
    public function __construct()
    {
        $this->fee_period = date('Y');
        $this->paid = false;

    }
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
     * Get memo
     *
     * @return string 
     */
    public function getMemo()
    {
        return $this->memo;
    }
    /**
     * Set fee_period
     *
     * @param integer $feePeriod
     * @return MemberFee
     */
    public function setFeePeriod($feePeriod)
    {
        $this->fee_period = $feePeriod;

        return $this;
    }

    /**
     * Get fee_period
     *
     * @return integer 
     */
    public function getFeePeriod()
    {
        return $this->fee_period;
    }

    /**
     * Set fee_amount_with_vat
     *
     * @param string $feeAmountWithVat
     * @return MemberFee
     */
    public function setFeeAmountWithVat($feeAmountWithVat)
    {
        $this->fee_amount_with_vat = $feeAmountWithVat;

        return $this;
    }

    /**
     * Get fee_amount_with_vat
     *
     * @return string 
     */
    public function getFeeAmountWithVat()
    {
        return $this->fee_amount_with_vat;
    }

    /**
     * Set member_id
     *
     * @param integer $memberId
     * @return MemberFee
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
     * Set memberfees
     *
     * @param \JYPS\RegisterBundle\Entity\Member $memberfees
     * @return MemberFee
     */
    public function setMemberfees(\JYPS\RegisterBundle\Entity\Member $memberfees = null)
    {
        $this->memberfees = $memberfees;

        return $this;
    }

    /**
     * Get memberfees
     *
     * @return \JYPS\RegisterBundle\Entity\Member 
     */
    public function getMemberfees()
    {
        return $this->memberfees;
    }

    /**
     * Set memberfee
     *
     * @param \JYPS\RegisterBundle\Entity\Member $memberfee
     * @return MemberFee
     */
    public function setMemberfee(\JYPS\RegisterBundle\Entity\Member $memberfee = null)
    {
        $this->memberfee = $memberfee;

        return $this;
    }

    /**
     * Get memberfee
     *
     * @return \JYPS\RegisterBundle\Entity\Member 
     */
    public function getMemberfee()
    {
        return $this->memberfee;
    }

    /**
     * Set reference_number
     *
     * @param string $referenceNumber
     * @return MemberFee
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->reference_number = $referenceNumber;

        return $this;
    }

    /**
     * Get reference_number
     *
     * @return string 
     */
    public function getReferenceNumber()
    {
        return $this->reference_number;
    }

    /**
     * Set due_date
     *
     * @param \DateTime $dueDate
     * @return MemberFee
     */
    public function setDueDate($dueDate)
    {
        $this->due_date = $dueDate;

        return $this;
    }

    /**
     * Get due_date
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->due_date;
    }

    /**
     * Set paid
     *
     * @param boolean $paid
     * @return MemberFee
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean 
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set memo
     *
     * @param string $memo
     * @return MemberFee
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }
    public function __toString()
{
    return $this->getMemberId();
}

}
