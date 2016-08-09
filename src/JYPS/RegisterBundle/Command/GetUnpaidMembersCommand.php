<?php
// src/JYPS/RegisterBundle/Command/GetUnpaidMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GetUnpaidMembersCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('get_unpaid')
			->setDescription('Print unpaid members for given period')
			->addOption('period', null, InputOption::VALUE_NONE, 'Fee period that is checked');
	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$period = $input->getOption('period');

		$repository = $this->getContainer()->get('doctrine')
			->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
			->where('m.membership_end_date >= :current_date AND m.magazine_preference = :magazine_pref')
			->setParameter('current_date', new \DateTime("now"))
			->setParameter('magazine_pref', 0)
			->getQuery();
		$period = 2015;
		$members = $query->getResult();

		foreach ($members as $member) {

			foreach ($member->getMemberFees() as $memberfee) {

				if ($memberfee->getFeePeriod() == $period && $memberfee->getPaid() == 0) {
					echo $member->getMemberId() . ";" . $member->getFirstName() . " " . $member->getSecondName() . " " . $member->getSurname() . "\n";
				}

			}
		}
	}

}
