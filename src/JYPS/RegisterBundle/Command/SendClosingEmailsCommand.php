<?php
// src/JYPS/RegisterBundle/Command/CloseUnpaidMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class SendClosingEmailsCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('send_closing_emails:year')
			->setDescription('Hack to run mails again IF error happened, for TODAY ONLY!!!')
			->addArgument('Year', InputArgument::OPTIONAL, 'Year of unpaid fee');
	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$i = 0;
		$year = $input->getArgument('Year');
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		$memberfees = $em->getRepository('JYPSRegisterBundle:MemberFee')
		                 ->findBy(array('fee_period' => $year, 'paid' => 0));

		foreach ($memberfees as $memberfee) {
			$member = $memberfee->getMemberfee();
			$memberenddate = $member->getMembershipEndDate();
			if ($memberenddate->format("Y-m-d") == date("Y-m-d")) {

				$emailConstraint = new EmailConstraint();
				$errors = "";
				$errors = $this->getContainer()->get('validator')->validateValue($member->getEmail(), $emailConstraint);
				if ($errors == "" && !is_null($member->getEmail()) && $member->getEmail() != "") {
					$message = \Swift_Message::newInstance()
						->setSubject("Jäsenyytesi JYPS Ry:ssä on lopetettu maksamattomien jäsenmaksujen takia")
						->setFrom("jasenrekisteri@jyps.fi")
						->setTo(array($member->getEmail()))
						->setBody($this->getContainer()->get('templating')->render("JYPSRegisterBundle:MemberFee:closed_member_unpaid.txt.twig"));
					$this->getContainer()->get('mailer')->send($message);
				}

				$i++;
			}
		}
		echo "Sent emails to " . $i . " members\n";
	}

}
