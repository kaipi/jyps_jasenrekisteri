<?php
// /src/JYPS/RegisterBundle/Controller/APIController.php
namespace JYPS\RegisterBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use JYPS\RegisterBundle\Entity\Member;
use JYPS\RegisterBundle\Entity\MemberFee;
use JYPS\RegisterBundle\Entity\Intrest;
use JYPS\RegisterBundle\Entity\MemberFeeConfig;
use FOS\RestBundle\Controller\FOSRestController;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\Annotations as Rest;


class APIController extends FOSRestController
{
	/**
	*@Rest\View
	*/
	public function getMembersAction()
	{
		$repository = $this->getDoctrine()
   		->getRepository('JYPSRegisterBundle:Member');

  		$query = $repository->createQueryBuilder('m')
   		 ->where('m.membership_end_date >= :current_date')
    	 ->setParameter('current_date', new \DateTime("now") )
         ->orderBy('m.surname', 'ASC')
    	 ->getQuery();
  
  		 $members = $query->getResult();	
		 return $members;
		
	}
	
}