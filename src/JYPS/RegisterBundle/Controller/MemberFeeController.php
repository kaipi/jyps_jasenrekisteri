<?php

namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JYPS\RegisterBundle\Entity\MemberFee;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JYPS\RegisterBundle\Form\Type\MemberFeeType;

class MemberFeeController extends Controller
{	
	public function showAllAction()
	{
		$memberfeeconfigs = $this->getDoctrine()
		->getRepository('JYPSRegisterBundle:MemberFeeConfig')
		->findAll();
		
		$repository = $this->getDoctrine()
		->getRepository('JYPSRegisterBundle:MemberFee');
		$feeyears = $repository->createQueryBuilder('memberfee')
        ->select('memberfee.fee_period')
        ->where('memberfee.paid= :paymentstatus')
        ->orderBy('memberfee.fee_period', 'DESC')
        ->setParameter('paymentstatus', 0)
        ->distinct()
        ->getQuery();

		$distinct_years = $feeyears->getResult();

		return $this->render('JYPSRegisterBundle:MemberFee:show_memberfee.html.twig', array('memberfee_configs'=>$memberfeeconfigs,
																							'years'=>$distinct_years,));

	}
	public function showUnpaidFeesAction(Request $request)
	{
	    $year = $request->request->get('year');
		$memberfees = $this->getDoctrine()
		->getRepository('JYPSRegisterBundle:MemberFee')
		->findBy(array('fee_period' => $year, 'paid' => 0),
			     array('fee_period' => 'ASC'));

		return $this->render('JYPSRegisterBundle:MemberFee:show_unpaid_fees.html.twig', array('memberfees'=>$memberfees, 'year'=>$year));

	}
	public function markFeesPaidAction(Request $request)
	{

	    $fees = $request->get('Fees_to_be_marked');
	    $em = $this->getDoctrine()->getManager();

	    foreach($fees as $fee) {
	    	$markfee = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:MemberFee')
			->findOneBy(array('id' => $fee,));
	    	$markfee->setPaid(True);
	    	$em->flush();
	    }
	    return $this->showUnpaidFeesAction($request);
   	}
   	public function markOneFeeAsPaid(Request $request)
   	{
   		$feeid = $request->get('Fee_Id');
   		$memberid = $request->get("Member_id");
   		
   		$em = $this->getDoctrine()->getManager();
   		$markfee = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:MemberFee')
			->findOneBy(array('id' => $feeid,));
	    $markfee->setPaid(True);
	    $em->flush();

	    $member = $this->getDoctrine()
  			->getRepository('JYPSRegisterBundle:Member')
  			->findOneBy(array('member_id' => $memberid));

	    return $member->showAllAction($memberid);
   	}
	public function createMemberFeesAction(Request $request)
	{
		ini_set('max_execution_time', 300);
		$members = $this->getDoctrine()
		->getRepository('JYPSRegisterBundle:Member')
		->findAll();
		$total_amount = 0;
		$total_qty = 0;

		foreach($members as $member) {
		    
			$memberFeeConfig = $member->getMemberType();

			//Do not create fees for membertypes where it's prevented
			if( $memberFeeConfig->getCreatefees() == "JOIN_ONLY") {
				continue;
			}
			//Check that fee does not exists fe. already joined members who get fee when joining.
			$createfee = TRUE;
			$fees = $member->getMemberFees();
			foreach($fees as $fee) {
				if($fee->getFeePeriod() == date('Y')) {
					$createfee = FALSE;
					break;
				}
			}
			$duedate = new \DateTime('now');
			$duedate->add(new \DateInterval('P10D'));
			if ($createfee == TRUE) {
				$total_amount = $total_amount + $memberFeeConfig->getMemberfeeAmount();
				$total_qty    = $total_qty + 1;
				$memberfee = new MemberFee();
	            $memberfee->setMemberId($member->getMemberid());
	            $memberfee->setFeeAmountWithVat($memberFeeConfig->getMemberfeeAmount());
	            $memberfee->setReferenceNumber(date('Y').$member->getMemberId());
	            $memberfee->setDueDate($duedate);
	            $memberfee->setMemberFee($member);
	            $memberfee->setFeePeriod(date('Y'));

				$em = $this->getDoctrine()->getManager();
				$em->persist($memberfee);
				$em->flush();
			}

		}
		return $this->render('JYPSRegisterBundle:MemberFee:memberfee_creation_finished.html.twig', array('total_amount'=>$total_amount, 'total_qty'=>$total_qty));
	}

	public function readReferencePayments(Request $request) 
	{
		
	}
}
