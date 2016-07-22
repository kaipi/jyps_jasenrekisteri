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
class SystemParameter implements UserInterface, \Serializable {
/**
 * @ORM\Column(type="integer")
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="AUTO")
 */
	private $id;
	/**
	 * @ORM\Column(type="string")
	 */
	private $key;
	/**
	 * @ORM\Column(type="integer")
	 */
	private $int_value;
	/**
	 * @ORM\Column(type="string")
	 */
	private $string_value;
	/**
	 * @ORM\Column(type="decimal")
	 */
	private $dec_value;
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $boolean_value;
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
		return array('ROLE_USER');
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
			$this->password,
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
	 * Set key
	 *
	 * @param string $key
	 * @return SystemParameter
	 */
	public function setKey($key) {
		$this->key = $key;

		return $this;
	}

	/**
	 * Get key
	 *
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Set int_value
	 *
	 * @param integer $intValue
	 * @return SystemParameter
	 */
	public function setIntValue($intValue) {
		$this->int_value = $intValue;

		return $this;
	}

	/**
	 * Get int_value
	 *
	 * @return integer
	 */
	public function getIntValue() {
		return $this->int_value;
	}

	/**
	 * Set string_value
	 *
	 * @param string $stringValue
	 * @return SystemParameter
	 */
	public function setStringValue($stringValue) {
		$this->string_value = $stringValue;

		return $this;
	}

	/**
	 * Get string_value
	 *
	 * @return string
	 */
	public function getStringValue() {
		return $this->string_value;
	}

	/**
	 * Set dec_value
	 *
	 * @param string $decValue
	 * @return SystemParameter
	 */
	public function setDecValue($decValue) {
		$this->dec_value = $decValue;

		return $this;
	}

	/**
	 * Get dec_value
	 *
	 * @return string
	 */
	public function getDecValue() {
		return $this->dec_value;
	}

	/**
	 * Set boolean_value
	 *
	 * @param boolean $booleanValue
	 * @return SystemParameter
	 */
	public function setBooleanValue($booleanValue) {
		$this->boolean_value = $booleanValue;

		return $this;
	}

	/**
	 * Get boolean_value
	 *
	 * @return boolean
	 */
	public function getBooleanValue() {
		return $this->boolean_value;
	}
}
