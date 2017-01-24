<?php

namespace JYPS\RegisterBundle\Controller;

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
		$total_amount = 0;
		$total_qty = 0;
		$memberfees = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:MemberFee')
			->findBy(array('fee_period' => $year, 'paid' => 0),
				array('member_id' => 'ASC'));
		$ok_fees = array();
		foreach ($memberfees as $memberfee) {
			$member = $memberfee->getMemberFee();

			if ($member->getMembershipEndDate() > new \DateTime("now")) {
				$total_amount = $total_amount + $memberfee->getFeeAmountWithVat();
				$total_qty++;
				$ok_fees[] = $memberfee;
			}
		}
		return $this->render('JYPSRegisterBundle:MemberFee:show_unpaid_fees.html.twig', array('memberfees' => $ok_fees, 'year' => $year, 'qty' => $total_qty, 'total_amount' => $total_amount));

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
				$member->getParent() == NULL &&
				($member->getReminderSentDate() <= $treshold_date ||
					$member->getReminderSentDate() == NULL)) {
				$errors = "";
				$emailConstraint = new EmailConstraint();
				$errors = $this->get('validator')->validateValue($member->getEmail(), $emailConstraint);
				if ($errors == "") {
					$message = \Swift_Message::newInstance();
					$message->setSubject('JYPS ry jäsenmaksumuistutus')
						->setFrom('jasenrekisteri@jyps.fi')
						->setTo($member->getEmail())
						->setBody($this->renderView(
							'JYPSRegisterBundle:MemberFee:reminder_letter_email.txt.twig'));
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
			$memberparent = $member->getParent();
			if (!empty($memberparent)) {
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
		$this->get('session')->getFlashBag()->add(
			'notice',
			'Jäsenmaksut luotu, Kokonaissumma: ' . $total_amount . " Määrä(kpl): " . $total_qty . "");
		return $this->redirect($this->generateUrl('memberfees'));
	}
	public function sendOneMemberFeeEmailAction(Request $request) {

		$member_id = $request->request->get('member_id');

		$fee_period = date('Y');
		$bankaccount = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:SystemParameter')
			->findOneBy(array('key' => 'BankAccount'));

		$member = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:Member')
			->findOneBy(array('id' => $member_id));

		$memberfee = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:MemberFee')
			->findOneBy(array('member_id' => $member_id, 'fee_period' => $fee_period));
		if ($memberfee === null) {
			$memberfee = $this->getDoctrine()
				->getRepository('JYPSRegisterBundle:MemberFee')
				->findOneBy(array('member_id' => $member_id, 'fee_period' => $fee_period - 1));
		}
		$emailConstraint = new EmailConstraint();

		$errors = "";
		$errors = $this->get('validator')->validateValue($member->getEmail(), $emailConstraint);
		if ($errors == "" && !is_null($member->getEmail()) && $member->getEmail() != "") {
			if (\Swift_Validate::email($member->getEmail())) {
				$message = \Swift_Message::newInstance()
					->setSubject("JYPS Ry:n jäsenmaksu vuodelle " . date('Y'))
					->setFrom("jasenrekisteri@jyps.fi")
					->setTo(array($member->getEmail()))
					->attach(\Swift_Attachment::fromPath($this->makeMemberCard($member)))
					->setBody($this->renderView('JYPSRegisterBundle:MemberFee:memberfee_email.txt.twig',
						array('member' => $member,
							'memberfee' => $memberfee,
							'bankaccount' => $bankaccount,
							'virtualbarcode' => $memberfee->getVirtualBarcode($bankaccount),
							'year' => date("Y"))));

			}
			$childs = $member->getChildren();
			//attach also all childmembers cards to mail
			foreach ($childs as $child) {
				$message->attach(\Swift_Attachment::fromPath($this->makeMemberCard($child)));
			}
			$this->get('mailer')->send($message);
		}
		$this->get('session')->getFlashBag()->add(
			'notice',
			'Sähköposti lähetetty');
		return $this->redirect($this->generateUrl('member', array("memberid" => $member->getMemberId())));

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
						->setFrom("jasenrekisteri@jyps.fi")
						->setTo(array($member->getEmail()))
						->attach(\Swift_Attachment::fromPath($this->makeMemberCard($member)))
						->setBody($this->renderView('JYPSRegisterBundle:MemberFee:memberfee_email.txt.twig',
							array('member' => $member,
								'memberfee' => $memberfee,
								'bankaccount' => $bankaccount,
								'virtualbarcode' => $memberfee->getVirtualBarcode($bankaccount),
								'year' => date("Y"))));

					$childs = $member->getChildren();

					//attach also all childmembers cards to mail
					foreach ($childs as $child) {
						$message->attach(\Swift_Attachment::fromPath($this->makeMemberCard($child)));
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

	public function paytrailPaymentAction(Request $request, $reference) {

		$memberfee = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:MemberFee')
			->findOneBy(array('reference_number' => $reference));
		if ($memberfee->getPaid()) {
			return $this->render('JYPSRegisterBundle:MemberFee:payment_already_paid.html.twig');
		}
		$member = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:Member')
			->findOneBy(array('id' => $memberfee->getMemberId()));

		$merchant_id = $this->GetSystemParameter("PaytrailMerchantId")->getStringValue();
		$authcode = $this->GetSystemParameter("PaytrailMerchantAuthCode")->getStringValue();
		$order_number = $memberfee->getReferencenumber();
		$order_description = "Jasenmaksu;Jasen:" . $member->getMemberId();
		$return_address = $this->GetSystemParameter("PaymentCompleteURL")->getStringValue();
		$cancel_address = $this->GetSystemParameter("PaymentCancelledURL")->getStringValue();
		$notify_address = $this->GetSystemParameter("PaymentCompleteURL")->getStringValue();
		$contact_firstname = $member->getFirstName();
		$contact_lastname = $member->getSurname();
		$contact_email = $member->getEmail();
		$contact_addr_street = $member->getStreetAddress();
		$contact_addr_zip = $member->getPostalCode();
		$contact_addr_city = $member->getCity();
		$contact_addr_country = $member->getCountry();
		$memberfee_amount = $memberfee->getFeeAmountWithVat();

		$authcode = strtoupper(md5($authcode . "|" .
			$merchant_id . "|" .
			$memberfee_amount . "|" .
			$order_number . "|" .
			"|" .
			$order_description . "|" .
			"EUR|" .
			$return_address . "|" .
			$cancel_address . "|" .
			"|" .
			$notify_address . "|" .
			"S1|" .
			"fi_FI|" .
			"|" .
			"1|" .
			"|" .
			""));

		return $this->render('JYPSRegisterBundle:MemberFee:paytrail_payment.html.twig', array('merchant_id' => $merchant_id,
			'order_number' => $order_number,
			'order_description' => $order_description,
			'return_address' => $return_address,
			'cancel_address' => $cancel_address,
			'notify_address' => $notify_address,
			'contact_email' => $contact_email,
			'contact_firstname' => $contact_firstname,
			'contact_lastname' => $contact_lastname,
			'contact_addr_street' => $contact_addr_street,
			'contact_addr_zip' => $contact_addr_zip,
			'contact_addr_city' => $contact_addr_city,
			'contact_addr_country' => $contact_addr_country,
			'memberfee_amount' => $memberfee_amount,
			'authcode' => $authcode,
			'memberfee_year' => $memberfee->getFeePeriod(),
			'memberfullname' => $member->getFullName(),
			'memberid' => $member->getMemberid(),
			'membertype' => $member->getMemberType()));

	}

	public function paymentCompleteAction(Request $request) {
		$ordernumber = $request->query->get('ORDER_NUMBER');
		$return_auth = $request->query->get('RETURN_AUTHCODE');
		$timestamp = $request->query->get('TIMESTAMP');
		$payment_method = $request->query->get('METHOD');
		$payment_transaction_id = $request->query->get('PAID');
		$check_hash = strtoupper(md5($ordernumber . "|" .
			$timestamp . "|" .
			$payment_transaction_id . "|" .
			$payment_method . "|" .
			$this->GetSystemParameter("PaytrailMerchantAuthCode")->getStringValue()));

		if ($check_hash == $return_auth) {
			$fee = $this->getDoctrine()
				->getRepository('JYPSRegisterBundle:MemberFee')
				->findOneBy(array('reference_number' => $ordernumber));
			$fee->setPaid(True);
			$em = $this->getDoctrine()->getManager();
			$em->persist($fee);
			$em->flush();
			return $this->render('JYPSRegisterBundle:MemberFee:paytrail_payment_completed.html.twig');
		} else {
			return $this->render('JYPSRegisterBundle:MemberFee:paytrail_payment_failed.html.twig', array('return_auth' => $return_auth));
		}

	}
	public function paymentCancelledAction(Request $request) {
		return $this->render('JYPSRegisterBundle:MemberFee:paytrail_payment_failed.html.twig');

	}
	private function makeMemberCard($member) {

		$baseimage = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/images/JYPS_Jasenkortti.png');
		$font = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/fonts/LucidaGrande.ttf');
		$card_image = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/savedCards/');

		return MemberCardGenerator::generateMembershipCard($member, $baseimage, $font, $card_image);

	}
	private function getSystemParameter($parameter_name) {
		$value = $this->getDoctrine()
			->getRepository('JYPSRegisterBundle:SystemParameter')
			->findOneBy(array('key' => $parameter_name));
		return $value;
	}
}
