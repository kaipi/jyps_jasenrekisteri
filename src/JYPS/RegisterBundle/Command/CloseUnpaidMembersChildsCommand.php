<?php
// src/JYPS/RegisterBundle/Command/CloseUnpaidMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CloseUnpaidMembersChildsCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('close_child_members')
			->setDescription('Close all childmembers if host is closed');
	}
	protected function execute(InputInterface $input, OutputInterface $output) {

		$repository = $this->getContainer()->get('doctrine')
			->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
			->where('m.membership_end_date >= :current_date AND m.parent IS NOT NULL')
			->setParameter('current_date', new \DateTime("now"))
			->getQuery();
		$members = $query->getResult();
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		foreach ($members as $member) {
			$parent = $member->getParent();
			#parent is closed
			if ($parent->getMembershipEndDate() <= new \Datetime("now")) {
				$member->setMembershipEndDate(new \DateTime('now'));
				$em->flush($member);
				print $member->getMemberId() . " parent: " . $parent->getMemberId() . "\n";
			}
		}

	}
}
