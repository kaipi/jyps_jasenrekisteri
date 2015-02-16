<?php

namespace JYPS\RegisterBundle\Controller;

use Endroid\QrCode\QrCode;
use JYPS\RegisterBundle\Entity\MemberFee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class MemberFeeController extends Controller {
	public function showAllAction() {
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

		return $this->render('JYPSRegisterBundle:MemberFee:show_memberfee.html.twig', array('memberfee_configs' => $memberfeeconfigs,
			'years' => $distinct_years));

	}
	public function showUnpaidFeesAction(Request $request) {
		$year = $request->request->get('year');
		$memberfees = $this->getDoctrine()
		                   ->getRepository('JYPSRegisterBundle:MemberFee')
		                   ->findBy(array('fee_period' => $year, 'paid' => 0),
			                   array('member_id' => 'ASC'));
		$ok_fees = array();
		foreach ($memberfees as $memberfee) {
			$member = $memberfee->getMemberFee();

			if ($member->getMembershipEndDate() > new \DateTime("now")) {
				$ok_fees[] = $memberfee;
			}
		}
		return $this->render('JYPSRegisterBundle:MemberFee:show_unpaid_fees.html.twig', array('memberfees' => $ok_fees, 'year' => $year));

	}
	public function sendReminderLetterAction(Request $request) {
		$join_date_limit = $request->request->get('join_date_limit');

		$memberfees = $this->getDoctrine()
		                   ->getRepository('JYPSRegisterBundle:MemberFee')
		                   ->findBy(array('paid' => 0),
			                   array('member_id' => 'ASC'));
		$qty = 0;
		$error_qty = 0;
		$error_members = array();
		//1month treshold from previous reminder
		$treshold_date = new \Datetime("now");
		$treshold_date->sub(new \DateInterval('P1M'));

		foreach ($memberfees as $memberfee) {
			$member = $memberfee->getMemberFee();
			if ($member->getMembershipStartDate()->format('Y-m-d') < $join_date_limit &&
				$member->getMembershipEndDate() > new \DateTime("now") &&
				$member->getEmail() != "" &&
				($member->getReminderSentDate() <= $treshold_date ||
					$member->getReminderSentDate() == NULL)) {
				$errors = "";
				$emailConstraint = new EmailConstraint();
				$errors = $this->get('validator')->validateValue($member->getEmail(), $emailConstraint);
				if ($errors == "") {
					$message = \Swift_Message::newInstance();
					$message->setSubject('JYPS ry jäsenmaksumuistutus')
					        ->setFrom('pj@jyps.fi')
					        ->setTo($member->getEmail())
					        ->setBody($this->renderView(
						       'JYPSRegisterBundle:MemberFee:reminder_letter_last_reminder.txt.twig'));
					$this->get('mailer')->send($message);
					$qty++;
					$em = $this->getDoctrine()->getManager();
					$member->setReminderSentDate(new \DateTime("now"));
					$em->flush($member);
				} else {
					$error_qty++;
					$error_members[] = $member;
				}
			}
		}

		return $this->render('JYPSRegisterBundle:MemberFee:sent_reminder_report.html.twig', array('reminder_qty' => $qty, 'error_qty' => $error_qty, 'error_members' => $error_members));
	}
	public function markFeesPaidAction(Request $request) {

		$fees = $request->get('Fees_to_be_marked');
		$em = $this->getDoctrine()->getManager();

		foreach ($fees as $fee) {
			$markfee = $this->getDoctrine()
			                ->getRepository('JYPSRegisterBundle:MemberFee')
			                ->findOneBy(array('id' => $fee));
			$markfee->setPaid(True);
			$em->flush($markfee);
		}
		return $this->showUnpaidFeesAction($request);
	}
	public function markOneFeeAsPaidAction(Request $request) {
		$feeid = $request->get('Fee_Id');
		$memberid = $request->get("Member_id");

		$em = $this->getDoctrine()->getManager();
		$markfee = $this->getDoctrine()
		                ->getRepository('JYPSRegisterBundle:MemberFee')
		                ->findOneBy(array('id' => $feeid));
		$markfee->setPaid(True);
		$em->flush($markfee);

		$member = $this->getDoctrine()
		               ->getRepository('JYPSRegisterBundle:Member')
		               ->findOneBy(array('member_id' => $memberid));

		return $member->showAllAction($memberid);
	}

	public function createMemberFeesAction(Request $request) {
		$d = $request->request->get('due_date');
		$duedate = new \DateTime($d);

		$em = $this->getDoctrine()->getManager();

		$repository = $this->getDoctrine()
		                   ->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
		                    ->where('m.membership_end_date >= :current_date')
		                    ->setParameter('current_date', new \DateTime("now"))
		                    ->getQuery();
		$members = $query->getResult();
		$total_amount = 0;
		$total_qty = 0;

		foreach ($members as $member) {
			/*if member is child of familymember -> do not create fee */
			if (!empty($member->getParent())) {
				continue;
			}
			$memberFeeConfig = $member->getMemberType();

			//Do not create fees for membertypes where it's prevented
			if ($memberFeeConfig->getCreatefees() == "JOIN_ONLY") {
				continue;
			}

			//Check that fee does not exists fe. already joined members who get fee when joining.
			$createfee = TRUE;
			$fees = $member->getMemberFees();
			foreach ($fees as $fee) {
				if ($fee->getFeePeriod() == date('Y')) {
					$createfee = FALSE;
					break;
				}
			}
			//Fee is prepaid, create fee and mark paid, unmark member prepaid status
			if ($member->getNextMemberfeePaid() == TRUE) {
				$memberfee_prepaid = TRUE;
				$member->setNextMemberfeePaid(FALSE);
				$em = $this->getDoctrine()->getManager();
				$em->persist($member);
				$em->flush($member);
			} else {
				$memberfee_prepaid = FALSE;
			}

			if ($createfee == TRUE) {
				$total_amount = $total_amount + $memberFeeConfig->getMemberfeeAmount();
				$total_qty = $total_qty + 1;
				$memberfee = new MemberFee();
				$memberfee->setMemberId($member->getMemberid());
				$memberfee->setFeeAmountWithVat($memberFeeConfig->getMemberfeeAmount());
				$memberfee->setReferenceNumber(date('Y') . $member->getMemberId());
				$memberfee->setDueDate($duedate);
				$memberfee->setMemberFee($member);
				$memberfee->setFeePeriod(date('Y'));
				$memberfee->setPaid($memberfee_prepaid);

				$em = $this->getDoctrine()->getManager();
				$em->persist($memberfee);
				$em->flush($memberfee);

			}

		}
		return $this->render('JYPSRegisterBundle:MemberFee:memberfee_creation_finished.html.twig', array('total_amount' => $total_amount, 'total_qty' => $total_qty));
	}
	public function sendMemberFeeEmailsAction(Request $request) {
		$errors = 0;
		$sent = 0;
		$em = $this->getDoctrine()->getManager();

		$bankaccount = $this->getDoctrine()
		                    ->getRepository('JYPSRegisterBundle:SystemParameter')
		                    ->findOneBy(array('key' => 'BankAccount'));

		$repository = $this->getDoctrine()
		                   ->getRepository('JYPSRegisterBundle:Member');
		$query = $repository->createQueryBuilder('m')
		                    ->where('m.membership_end_date >= :current_date AND m.membership_start_date <= :period_start')
		                    ->setParameter('current_date', new \DateTime("now"))
		                    ->setParameter('period_start', new \DateTime("first day of January " . date('Y')))
		                    ->getQuery();

		$members = $query->getResult();
		foreach ($members as $member) {

			$memberfee = $this->getDoctrine()
			                  ->getRepository('JYPSRegisterBundle:MemberFee')
			                  ->findOneBy(array('member_id' => $member->getId(),
				                  'fee_period' => date('Y'),
				                  'email_sent' => NULL));

			if (empty($memberfee)) {
				continue;
			}
			$emailConstraint = new EmailConstraint();

			$errors = "";
			$errors = $this->get('validator')->validateValue($member->getEmail(), $emailConstraint);
			if ($errors == "" && !is_null($member->getEmail()) && $member->getEmail() != "") {
				if (\Swift_Validate::email($member->getEmail())) {
					$message = \Swift_Message::newInstance()
						->setSubject("JYPS Ry:n jäsenmaksu vuodelle " . date('Y'))
						->setFrom("pj@jyps.fi")
						->setTo(array($member->getEmail()))
						->attach(\Swift_Attachment::fromPath($this->generateMembershipCard($member)))
						->setBody($this->renderView('JYPSRegisterBundle:MemberFee:memberfee_email.txt.twig',
							array('member' => $member,
								'memberfee' => $memberfee,
								'bankaccount' => $bankaccount,
								'virtualbarcode' => $memberfee->getVirtualBarcode($bankaccount),
								'year' => date("Y"))));

					$childs = $member->getChildren();

					//attach also all childmembers cards to mail
					foreach ($childs as $child) {
						$this->generateMembershipCard($child);
						$message->attach(\Swift_Attachment::fromPath($this->generateMembershipCard($child)));
					}
					$this->get('mailer')->send($message);

					$memberfee->setEmailSent(1);
					$em->flush($memberfee);

					$sent++;
				}
			} else {
				$errors++;
			}
		}
		$this->get('session')->getFlashBag()->add(
			'notice',
			'Jäsenmaksut lähetetty, OK:' . $sent . " NOK:" . $errors . "");
		return $this->redirect($this->generateUrl('memberfees'));
	}

//copypasted, move to another class when more time

	private function generateMembershipCard($member) {

		$base_image_path = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/images/JYPS_Jasenkortti.png');
		$base_image = imagecreatefrompng($base_image_path);
		$output_image = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/savedCards/') . 'MemberCard_' . $member->getMemberId() . '.png';

		/* member data to image */

		$black = imagecolorallocate($base_image, 0, 0, 0);
		$memberid = $member->getMemberId();
		$join_year = $member->getMembershipStartDate()->format('Y');
		$font = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/fonts/LucidaGrande.ttf');

		imagettftext($base_image, 38, 0, 190, 500, $black, $font, $member->getFullName());
		imagettftext($base_image, 38, 0, 390, 555, $black, $font, $memberid);
		imagettftext($base_image, 38, 0, 390, 610, $black, $font, $join_year);

		/*qr code to image & serialize json for qr code*/
		$member_data = array('member_id' => $member->getMemberId(),
			'join_year' => $member->getMembershipStartDate()->format('Y'),
			'name' => $member->getFullName());
		$member_qr_data = json_encode($member_data);

		$qrCode = new QrCode();
		$qrCode->setSize(380);
		$qrCode->setText($member_qr_data);
		$qrCode = $qrCode->get('png');
		$qr_image = imagecreatefromstring($qrCode);
		imagecopy($base_image, $qr_image, 550, 22, 0, 0, imagesx($qr_image), imagesy($qr_image));
		/*write image to disk */
		imagepng($base_image, $output_image);

		return $output_image;
	}

}
