<?php
// src/JYPS/RegisterBundle/Command/CloseUnpaidMembersCommand.php

namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use JYPS\RegisterBundle\Entity\Member;



class TaskProcessorCommand extends ContainerAwareCommand
{
  protected function configure()
    {
        $this
            ->setName('task_processor:process')
            ->setDescription('Process task queue')
            ->addArgument('TaskType', InputArgument::OPTIONAL, 'Which type  to process?');
    }
}