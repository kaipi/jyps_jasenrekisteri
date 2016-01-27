<?php

namespace JYPS\RegisterBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User implements AdvancedUserInterface, \Serializable {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=100, nullable=true))
	 */
	private $salt;

	/**
	 * @ORM\Column(type="string", length=250)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=60, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $realname;

	/**
	 * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
	 */
	private $roles;

	public function __construct() {
		$this->isActive = true;

		$this->roles = new ArrayCollection();

	}

	/**
	 * @inheritDoc
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt() {
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @inheritDoc
	 */
	public function getRoles() {
		return $this->roles->toArray();
	}

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials() {
	}

	/**
	 * @see \Serializable::serialize()
	 */
	public function serialize() {
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
	public function unserialize($serialized) {
		list(
			$this->id,
			$this->username,
			$this->salt,
			$this->password
		) = unserialize($serialized);
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 * @return User
	 */
	public function setUsername($username) {
		$this->username = $username;

		return $this;
	}

	/**
	 * Set salt
	 *
	 * @param string $salt
	 * @return User
	 */
	public function setSalt($salt) {
		$this->salt = $salt;

		return $this;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 * @return User
	 */
	public function setPassword($password) {
		$this->password = $password;

		return $this;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 * @return User
	 */
	public function setEmail($email) {
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set isActive
	 *
	 * @param boolean $isActive
	 * @return User
	 */
	public function setActive($active) {
		$this->isActive = $active;

		return $this;
	}

	/**
	 * Get isActive
	 *
	 * @return boolean
	 */
	public function getActive() {
		return $this->isActive;
	}

	/**
	 * Set realname
	 *
	 * @param string $realname
	 * @return User
	 */
	public function setRealname($realname) {
		$this->realname = $realname;

		return $this;
	}

	/**
	 * Get realname
	 *
	 * @return string
	 */
	public function getRealname() {
		return $this->realname;
	}

	public function isAccountNonExpired() {
		return true;
	}

	public function isAccountNonLocked() {
		return true;
	}

	public function isCredentialsNonExpired() {
		return true;
	}

	public function isEnabled() {
		return $this->isActive;
	}
	public function isActive() {
		return $this->isActive;
	}
	/**
	 * Add roles
	 *
	 * @param \JYPS\RegisterBundle\Entity\Role $roles
	 * @return User
	 */
	public function addRole(\JYPS\RegisterBundle\Entity\Role $roles) {
		$this->roles[] = $roles;

		return $this;
	}

	/**
	 * Remove roles
	 *
	 * @param \JYPS\RegisterBundle\Entity\Role $roles
	 */
	public function removeRole(\JYPS\RegisterBundle\Entity\Role $roles) {
		$this->roles->removeElement($roles);
	}
}
