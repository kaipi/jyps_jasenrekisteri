<?php
// /src/JYPS/RegisterBundle/Controller/APIController.php
namespace JYPS\RegisterBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use JYPS\RegisterBundle\Entity\Member;
use JYPS\RegisterBundle\Entity\MemberFee;
use JYPS\RegisterBundle\Entity\Intrest;
use JYPS\RegisterBundle\Entity\MemberFeeConfig;
use FOS\RestBundle\Controller\FOSRestController;


class APIController extends FOSRestController
{
	public function getGenderDistribution()
	{

	}
	public function getMemberAmountsPerYear() 
	{

	}
	public function getAgeGroups()
	{

	}
}