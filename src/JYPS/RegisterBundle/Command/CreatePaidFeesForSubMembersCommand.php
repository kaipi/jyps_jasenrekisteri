<?php
// src/JYPS/RegisterBundle/Command/CreatePaidFeesForSubMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use JYPS\RegisterBundle\Entity\MemberFee;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePaidFeesForSubMembersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('createfees_for_familymembers')
            ->setDescription('create missing fees + mark as paid')
            ->addOption('dryrun', null, InputOption::VALUE_NONE, 'If set, no modifications is done');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryrun = $input->getOption('dryrun');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $repository = $this->getContainer()->get('doctrine')
            ->getRepository('JYPSRegisterBundle:Member');

        $query = $repository->createQueryBuilder('m')
            ->where('m.parent is NOT NULL')
            ->getQuery();

        $members = $query->getResult();
        foreach ($members as $member) {
            $lastfee = $member->getMembershipStartDate()->format('Y');
            $memberfees = $member->getMemberFees();
            $memberFeeConfig = $member->getMemberType();
            //   $lastfee = date('Y');
            //first every fee as paid
            foreach ($memberfees as $fee) {
                $lastfee = $fee->getFeePeriod();
                if ($fee->getPaid() === false) {
                    if ($dryrun === false) {
                    }
                }
            }
            if ($memberfees === null) {
                $lastfee = $lastfee - 1;
            }
            if ($lastfee < date('Y')) {
                //then create missing fees
                echo $member->getMemberId() . " " . $lastfee . "\n";

                $i = $lastfee;
                do {
                    $i++;
                    echo "will create fee for period " . $i . "\n";
                    $memberFeeConfig = $member->getMemberType();
                    if ($memberFeeConfig === null) {
                        echo "there was a problem, aborting \n";
                        continue;
                    }
                    $memberfee = new MemberFee();
                    $memberfee->setMemberId($member->getMemberid());
                    $memberfee->setFeeAmountWithVat($memberFeeConfig->getMemberfeeAmount());
                    $memberfee->setReferenceNumber($i . $member->getMemberId());
                    $memberfee->setDueDate(new \DateTime($i . "-03-31"));
                    $memberfee->setMemberFee($member);
                    $memberfee->setFeePeriod($i);
                    $memberfee->setPaid(true);
                    $em->persist($memberfee);
                    $em->flush($memberfee);
                } while ($i < date('Y'));
            }
        }
    }
}
