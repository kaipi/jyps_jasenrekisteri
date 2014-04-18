<?php

namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ConfigurationController extends Controller
{

	public function showAllAction()
	{
		$sysparams = $this->getDoctrine()
		->getRepository('JYPSRegisterBundle:SystemParameter')
		->findAll();

		return $this->render('JYPSRegisterBundle:Configuration:show_configuration.html.twig', array('sysparams'=>$sysparams,));

	}
}
