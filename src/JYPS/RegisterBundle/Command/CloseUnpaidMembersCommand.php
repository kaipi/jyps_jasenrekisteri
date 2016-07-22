<?php
// src/JYPS/RegisterBundle/Command/CloseUnpaidMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class CloseUnpaidMembersCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('close_unpaid_members:year')
			->setDescription('Close all members to TODAY which have open memberfee on specified year / or ALL')
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
			if ($member->getMembershipEndDate() > new \DateTime("now")) {
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

				$member->setMembershipEndDate(new \DateTime('now'));
				$em->flush($member);
				$members[] = $member;
				$i++;
			}
		}
		$message = \Swift_Message::newInstance()
			->setSubject("JYPS Ry jäsentensulkuajo " . date("D-M-Y"))
			->setFrom("jasenrekisteri@jyps.fi")
			->setTo(array("jasenrekisteri@jyps.fi"))
			->setBody($this->getContainer()->get('templating')->render("JYPSRegisterBundle:MemberFee:closed_member_unpaid_infomail.txt.twig",
				array('members' => $members)));
		$this->getContainer()->get('mailer')->send($message);

		echo "Closed " . $i . " members\n";
	}

}
