<?php
namespace JYPS\RegisterBundle\Controller;
use JYPS\RegisterBundle\Entity\SystemParameter;

class SystemParameterHelper {

	public static function getSystemParameter($parameter_name) {
		$value = new SystemParameter();
		$value = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:SystemParameter')
			->findOneBy(array('key' => $parameter_name));
		return $value;
	}
}
