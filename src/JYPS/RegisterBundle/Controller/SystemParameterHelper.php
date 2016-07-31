<?php
namespace JYPS\RegisterBundle\Controller;

class SystemParameterHelper {

	public static function getSystemParameter($parameter_name) {
		$value = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:SystemParameter')
			->findOneBy(array('key' => $parameter_name));
		return $value;
	}
}
