<?php
// src/JYPS/RegisterBundle/Command/RemoveDuplicateMemberCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDuplicateMemberCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('remove_duplicate_members')
			->setDescription('Fix error in member join handling, just for easing up until bug fixed')
			->addOption('dryrun', null, InputOption::VALUE_NONE, 'If set, no modifications is done');
	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$dryrun = $input->getOption('dryrun');
		$parent_member = null;
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$repository = $this->getContainer()->get('doctrine')
			->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
			->where('m.birth_year = 0 AND m.firstname = :firstname AND m.surname = :surname AND m.membership_end_date >= :current_date')
			->setParameter('firstname', '')
			->setParameter('surname', '')
			->setParameter('current_date', new \DateTime('now'))
			->getQuery();

		$members = $query->getResult();

		foreach ($members as $member) {
			if ($dryrun !== true) {
				if ($member->getParent() != "") {
					$parent_member = $member->getParent();
					$member->setParent(NULL);
					$em->flush($parent_member);
				}
				$member->setMembershipEndDate(new \DateTime('now'));
				$em->flush($member);
			}
			echo $member->getMemberId() . " " . $member->getFullName() . "\n";
		}
	}

}
