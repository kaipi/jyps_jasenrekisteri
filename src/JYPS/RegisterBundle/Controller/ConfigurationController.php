<?php

namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ConfigurationController extends Controller
{

    public function showAllAction()
    {
        $sysparams = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:SystemParameter')
            ->findAll();

        return $this->render('JYPSRegisterBundle:Configuration:show_configuration.html.twig', array('sysparams' => $sysparams));

    }
}
