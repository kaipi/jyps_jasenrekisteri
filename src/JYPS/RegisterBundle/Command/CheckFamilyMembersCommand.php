<?php
// src/JYPS/RegisterBundle/Command/CheckFamilyMembers.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckFamilyMembersCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('check_family_members')
			->setDescription('Check that all familymembers fees is correctly marked as paid and main member ')
			->addOption('dryrun', null, InputOption::VALUE_NONE, 'If set, no modifications is done');

	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$dryrun = $input->getOption('dryrun');

		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$repository = $this->getContainer()->get('doctrine')
		                   ->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
		                    ->where('m.membership_end_date >= :current_date AND m.parent is NOT NULL')
		                    ->setParameter('current_date', new \DateTime("now"))
		                    ->getQuery();

		$members = $query->getResult();

		foreach ($members as $member) {

			$memberfees = $member->getMemberFees();
			foreach ($memberfees as $fee) {
				if ($fee->getPaid() == false) {
					echo $member->getMemberId() . " " . $fee->getFeePeriod() . " " . $member->getFullname() . "\n";
					if ($dryrun == false) {
						$fee->setPaid(true);
						$em->flush($fee);
					}
				}

			}
		}

	}

}