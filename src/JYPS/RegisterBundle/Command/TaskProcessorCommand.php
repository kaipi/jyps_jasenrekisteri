<?php
/**
 * Task processor
 * src/JYPS/RegisterBundle/Command/TaskProcessorCommand.php */
namespace JYPS\RegisterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaskProcessorCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('task_processor:process')
            ->setDescription('Process task queue')
            ->addArgument(
                'TaskType', InputArgument::OPTIONAL,
                'Which type  to process?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $continueprocess = true;
        $task_type = $input->getArgument('TaskType');
        if ($task_type !== "") {
            $output->writeln("Processing type: " . $task_type);
        } else {
            $output->writeln("Processing all types");
            while ($continueprocess === true) {
                sleep(5);
                $output->writeln("Processed");
            }

        }
    }

}
