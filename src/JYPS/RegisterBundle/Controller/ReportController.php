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
  		$total_members = 0;
  		$male_qty = 0;
  		$female_qty = 0;
  		$female_juniors = 0;
  		$male_juniors = 0;
  		$e_magazine = 0;
  		$join_year_qty = [];
  		$zip_codes = [];
  		$membership_types = [];
  		$members = $query->getResult();
  		foreach($members as $member) {
	  		$total_members++;
	  		if ($member->getGender() == False) {
	  			$female_qty++;
	  		}
	  		else {
		  		$male_qty++;
	  		}
	  		if($member->getMagazinePreference() == True) {
	  			$e_magazine++;
	  		}
	  		if(date('Y') - $member->getBirthYear() < 18) {
	  			if ($member->getGender() == False) {
	  				$female_juniors++;
	  			}
	  			else {
	  				$male_juniors++;
	  			}
	  		}

	  	}
	return $this->render('JYPSRegisterBundle:Member:reports.html.twig', 
		array('total_members' => $total_members,
    		  'male_qty' => $male_qty, 
    		  'female_qty' => $female_qty,
    		  'e_magazine' => $e_magazine,
    		  'male_juniors' => $male_juniors,
    		  'female_juniors' => $female_juniors));
	}
}