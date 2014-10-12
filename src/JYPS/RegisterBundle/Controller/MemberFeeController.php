<?php

namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JYPS\RegisterBundle\Entity\MemberFee;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JYPS\RegisterBundle\Form\Type\MemberFeeType;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

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
			     array('member_id' => 'ASC'));
		$ok_fees = array();
		foreach($memberfees as $memberfee) {
			$member = $memberfee->getMemberFee();

			if ($member->getMembershipEndDate() > new \DateTime("now")) {
				$ok_fees[] = $memberfee;
			}
		}
		return $this->render('JYPSRegisterBundle:MemberFee:show_unpaid_fees.html.twig', array('memberfees'=>$ok_fees, 'year'=>$year));

	}
	public function sendReminderLetterAction(Request $request)
	{
		$join_date_limit = $request->request->get('join_date_limit');
		
		$memberfees = $this->getDoctrine()
		->getRepository('JYPSRegisterBundle:MemberFee')
		->findBy(array('paid' => 0),
			     array('member_id' => 'ASC'));
		$qty = 0;
		$error_qty=0;
		$error_members = array();
		//1month treshold from previous reminder
		$treshold_date = new \Datetime("now");
		$treshold_date->sub(new \DateInterval('P1M'));

		foreach($memberfees as $memberfee) {
			$member = $memberfee->getMemberFee();
			if ($member->getMembershipStartDate()->format('Y-m-d') < $join_date_limit &&
				$member->getMembershipEndDate() > new \DateTime("now") &&
				$member->getEmail() != "" &&
				($member->getReminderSentDate() <= $treshold_date ||
				 $member->getReminderSentDate() == NULL )) {
				$errors = "";
			    $emailConstraint = new EmailConstraint();
				$errors = $this->get('validator')->validateValue($member->getEmail(), $emailConstraint);
				if ($errors == "") {
					$message = \Swift_Message::newInstance();
					$message->setSubject('JYPS ry jäsenmaksu muistutus')
				    		->setFrom('pj@jyps.fi')
						    ->setTo($member->getEmail())
						    ->setBody($this->renderView(
						      'JYPSRegisterBundle:MemberFee:reminder_letter_email.txt.twig'));
	    			$this->get('mailer')->send($message);
	    			$qty++;
	    		    $em = $this->getDoctrine()->getManager();
	    			$member->setReminderSentDate(new \DateTime("now"));
	    			$em->flush($member);
    			}
    			else {
    				$error_qty++;
    				$error_members[] = $member;
    			}
			} 
		}
		
		return $this->render('JYPSRegisterBundle:MemberFee:sent_reminder_report.html.twig', array('reminder_qty'=>$qty, 'error_qty'=>$error_qty, 'error_members'=>$error_members));
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
	    	$em->flush($markfee);
	    }
	    return $this->showUnpaidFeesAction($request);
   	}
   	public function markOneFeeAsPaidAction(Request $request)
   	{
   		$feeid = $request->get('Fee_Id');
   		$memberid = $request->get("Member_id");
   		
   		$em = $this->getDoctrine()->getManager();
   		$markfee = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:MemberFee')
			->findOneBy(array('id' => $feeid,));
	    $markfee->setPaid(True);
	    $em->flush($markfee);

	    $member = $this->getDoctrine()
  			->getRepository('JYPSRegisterBundle:Member')
  			->findOneBy(array('member_id' => $memberid));

	    return $member->showAllAction($memberid);
   	}
	public function createMemberFeesAction(Request $request)
	{
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
				$em->flush($memberfee);
			}

		}
		return $this->render('JYPSRegisterBundle:MemberFee:memberfee_creation_finished.html.twig', array('total_amount'=>$total_amount, 'total_qty'=>$total_qty));
	}

}
