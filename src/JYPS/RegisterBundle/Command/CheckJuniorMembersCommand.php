<?php
// src/JYPS/RegisterBundle/Command/CheckJuniorMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckJuniorMembersCommand extends ContainerAwareCommand {
	protected function configure() {
		$this
			->setName('check_junior_members')
			->setDescription('Check all members and change membertype to adult and/or junior');
	}
	protected function execute(InputInterface $input, OutputInterface $output) {
		$i = 0;

		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$repository = $this->getContainer()->get('doctrine')
		                   ->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
		                    ->where('m.membership_end_date >= :current_date')
		                    ->setParameter('current_date', new \DateTime("now"))
		                    ->getQuery();

		$members = $query->getResult();
		$adult_member = $em->getRepository('JYPSRegisterBundle:MemberFeeConfig')
		                   ->findOneBy(array('member_type' => 1));

		foreach ($members as $member) {
			$membertype = $member->getMemberType();
			if (date('Y') - $member->getBirthYear() >= 18 && $membertype->getMemberType() == 2) {
				$i++;
				$member->setMembertype($adult_member);
				$em->flush($member);
				echo $member->getFullName() . " updated to Adult (born " . $member->getBirthYear() . ") \n";
			}

		}
	}

}
