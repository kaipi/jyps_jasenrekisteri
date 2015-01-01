<?php
// src/JYPS/RegisterBundle/Command/CloseUnpaidMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JYPS\RegisterBundle\Entity\Member;
use JYPS\RegisterBundle\Entity\MemberFee;



class CloseUnpaidMembersCommand extends ContainerAwareCommand
{
  protected function configure()
    {
        $this
            ->setName('close_unpaid_members:year')
            ->setDescription('Close all members to TODAY which have open memberfee on specified year / or ALL')
            ->addArgument('Year', InputArgument::OPTIONAL, 'Year of unpaid fee');
    }
     protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$i = 0;
	    $year = $request->request->get('year');
		$memberfees = $this->getDoctrine()
		->getRepository('JYPSRegisterBundle:MemberFee')
		->findBy(array('fee_period' => $year, 'paid' => 0),
			     array('member_id' => 'ASC'));
		$ok_fees = array();
		foreach($memberfees as $memberfee) {
			$member = $memberfee->getMemberFee();

			if ($member->getMembershipEndDate() > new \DateTime("now")) {
				$member->closeMemberWithEmail("unpaid_member_close.txt.twig", "Jäsenyytesi JYPS Ry:ssä on lopetettu maksamattomien jäsenmaksujen takia", "pj@jyps.fi");
				$i++;
			}
		}
		echo "Closed ". $i ." members";
    }
}