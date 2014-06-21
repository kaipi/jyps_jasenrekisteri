<?php
// src/JYPS/RegisterBundle/Controller/ReportController.php;
namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use JYPS\RegisterBundle\Entity\Member;
use JYPS\RegisterBundle\Entity\MemberFee;
use JYPS\RegisterBundle\Entity\Intrest;
use JYPS\RegisterBundle\Entity\IntrestConfig;
use JYPS\RegisterBundle\Entity\MemberFeeConfig;

class ReportController extends Controller 
{
	public function indexAction() {
		$repository = $this->getDoctrine()
   			->getRepository('JYPSRegisterBundle:Member');

  		$query = $repository->createQueryBuilder('m')
    		->where('m.membership_end_date >= :current_date')
    		->setParameter('current_date', new \DateTime("now") )
    		->getQuery();
  
  		$members = $query->getResult();

  		
	}
}