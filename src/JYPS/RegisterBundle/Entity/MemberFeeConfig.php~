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
class MemberFeeConfig implements UserInterface, \Serializable
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
    private $memberfee_name;
    /**
    * @ORM\Column(type="decimal")
    */ 
    private $memberfee_amount;
    /**
    * @ORM\Column(type="date")
    */ 
    private $valid_from;
    /**
    * @ORM\Column(type="date")
    */ 
    private $valid_to;
    /**
    * @ORM\Column(type="integer")
    */ 
    private $member_type;
    /**
    * @ORM\Column(type="string")
    */
    private $createfees;
    /**
    * @ORM\Column(type="boolean")
    */
    private $show_on_join_form;
    /**
    * @ORM\Column(type="boolean")
    */
    private $campaign_fee;
    /**
    * @ORM\Column(type="integer")
    */
    private $real_membertype;
    /**
    * @ORM\OneToMany(targetEntity="Member", mappedBy="member_type")
    * @ORM\JoinColumn(referencedColumnName="id")
    */
    protected $membertypes;
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
     * Set memberfee_name
     *
     * @param string $memberfeeName
     * @return MemberFeeConfig
     */
    public function setMemberfeeName($memberfeeName)
    {
        $this->memberfee_name = $memberfeeName;

        return $this;
    }

    /**
     * Get memberfee_name
     *
     * @return string 
     */
    public function getMemberfeeName()
    {
        return $this->memberfee_name;
    }

    /**
     * Set memberfee_amount
     *
     * @param string $memberfeeAmount
     * @return MemberFeeConfig
     */
    public function setMemberfeeAmount($memberfeeAmount)
    {
        $this->memberfee_amount = $memberfeeAmount;

        return $this;
    }

    /**
     * Get memberfee_amount
     *
     * @return string 
     */
    public function getMemberfeeAmount()
    {
        return $this->memberfee_amount;
    }

    /**
     * Set valid_from
     *
     * @param \DateTime $validFrom
     * @return MemberFeeConfig
     */
    public function setValidFrom($validFrom)
    {
        $this->valid_from = $validFrom;

        return $this;
    }

    /**
     * Get valid_from
     *
     * @return \DateTime 
     */
    public function getValidFrom()
    {
        return $this->valid_from;
    }

    /**
     * Set valid_to
     *
     * @param \DateTime $validTo
     * @return MemberFeeConfig
     */
    public function setValidTo($validTo)
    {
        $this->valid_to = $validTo;

        return $this;
    }

    /**
     * Get valid_to
     *
     * @return \DateTime 
     */
    public function getValidTo()
    {
        return $this->valid_to;
    }

    /**
     * Set createfees
     *
     * @param string $createfees
     * @return MemberFeeConfig
     */
    public function setCreatefees($createfees)
    {
        $this->createfees = $createfees;

        return $this;
    }

    /**
     * Get createfees
     *
     * @return string 
     */
    public function getCreatefees()
    {
        return $this->createfees;
    }

    /**
     * Set show_on_join_form
     *
     * @param boolean $showOnJoinForm
     * @return MemberFeeConfig
     */
    public function setShowOnJoinForm($showOnJoinForm)
    {
        $this->show_on_join_form = $showOnJoinForm;

        return $this;
    }

    /**
     * Get show_on_join_form
     *
     * @return boolean 
     */
    public function getShowOnJoinForm()
    {
        return $this->show_on_join_form;
    }

    public function getNameWithFeeAmount()
{
    return $this->memberfee_name." (".$this->memberfee_amount."eur)";
}


    /**
     * Set campaign_fee
     *
     * @param boolean $campaignFee
     * @return MemberFeeConfig
     */
    public function setCampaignFee($campaignFee)
    {
        $this->campaign_fee = $campaignFee;

        return $this;
    }

    /**
     * Get campaign_fee
     *
     * @return boolean 
     */
    public function getCampaignFee()
    {
        return $this->campaign_fee;
    }

    /**
     * Set real_membertype
     *
     * @param integer $realMembertype
     * @return MemberFeeConfig
     */
    public function setRealMembertype($realMembertype)
    {
        $this->real_membertype = $realMembertype;

        return $this;
    }

    /**
     * Get real_membertype
     *
     * @return integer 
     */
    public function getRealMembertype()
    {
        return $this->real_membertype;
    }

    /**
     * Set member
     *
     * @param \JYPS\RegisterBundle\Entity\Member $member
     * @return MemberFeeConfig
     */
    public function setMember(\JYPS\RegisterBundle\Entity\Member $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \JYPS\RegisterBundle\Entity\Member 
     */
    public function getMember()
    {
        return $this->member;
    }
    public function __toString()
    {
        return $this->memberfee_name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->membertype = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Add membertype
     *
     * @param \JYPS\RegisterBundle\Entity\Member $membertype
     * @return MemberFeeConfig
     */
    public function addMembertype(\JYPS\RegisterBundle\Entity\Member $membertype)
    {
        $this->membertype[] = $membertype;

        return $this;
    }

    /**
     * Remove membertype
     *
     * @param \JYPS\RegisterBundle\Entity\Member $membertype
     */
    public function removeMembertype(\JYPS\RegisterBundle\Entity\Member $membertype)
    {
        $this->membertype->removeElement($membertype);
    }

    /**
     * Set member_type
     *
     * @param integer $memberType
     * @return MemberFeeConfig
     */
    public function setMemberType($memberType)
    {
        $this->member_type = $memberType;

        return $this;
    }

    /**
     * Get member_type
     *
     * @return integer 
     */
    public function getMemberType()
    {
        return $this->member_type;
    }

    /**
     * Get membertype
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMembertypes()
    {
        return $this->membertype;
    }
}
