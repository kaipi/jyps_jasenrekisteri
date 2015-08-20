<?php
// src/JYPS/RegisterBundle/Command/CheckMemberTypesCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckMemberTypesCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('check_membertypes')
			->setDescription('Check that all memberfees for current or given year represent the membertype set, if not fix the fee')
			->addOption('dryrun', null, InputOption::VALUE_NONE, 'If set, no modifications is done');
	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$dryrun = $input->getOption('dryrun');
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$repository = $this->getContainer()->get('doctrine')
			->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
			->where('m.membership_end_date >= :current_date')
			->setParameter('current_date', new \DateTime("now"))
			->getQuery();

		$members = $query->getResult();

		foreach ($members as $member) {
			$membertype = $member->getMemberType();

			foreach ($member->getMemberFees() as $memberfee) {
				$feeperiod = $memberfee->getFeePeriod();
				if ($feeperiod == date('Y') && $membertype->getMemberfeeAmount() != $memberfee->getFeeAmountWithVat()) {
					echo $member->getMemberId() . " " . $feeperiod . " " . $member->getFullName() . " " . $memberfee->getFeeAmountWithVat() . " -> " . $membertype->getMemberFeeAmount() . " (" . $membertype->getMemberFeeName() . ")\n";
					if ($dryrun !== true) {
						$memberfee->setFeeAmountWithVat($membertype->getMemberFeeAmount());
						$em->flush($memberfee);
					}
				}
			}
		}
	}

}
