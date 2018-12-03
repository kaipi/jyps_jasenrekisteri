<?php
/**
 * PDF (reminder) generatory
 *  src/JYPS/RegisterBundle/Command/GeneratePDFInvoicesCommand.php
 **/

namespace JYPS\RegisterBundle\Command;

use Mpdf\Mpdf;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePDFInvoicesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('print_pdf_invoices')
            ->setDescription('Prints pdf (reminder) invoices')
            ->addArgument('Year', InputArgument::OPTIONAL, 'Year of invoices to print');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $i = 0;

        $mpdf = new Mpdf();

        $year = $input->getArgument('Year');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $memberfees = $em->getRepository('JYPSRegisterBundle:MemberFee')
            ->findBy(array('fee_period' => $year, 'paid' => 0));
        foreach ($memberfees as $memberfee) {
            $member = $memberfee->getMemberfee();
            if ($member->getMembershipEndDate() > new \DateTime("now")) {
                $html = $this->getContainer()->get('templating')
                    ->render(
                        "JYPSRegisterBundle:MemberFee:reminder_pdf.html.twig",
                        array('member' => $member, 'memberfee' => $memberfee,
                            'invoiceDate' => (new \DateTime())->format("d.n.Y"))
                    );
                $mpdf->WriteHTML($html);
                $mpdf->Output(
                    "invoice_" . $member->getMemberId() .
                    ".pdf", \Mpdf\Output\Destination::FILE
                );
                $i++;
                echo "Generating reminder for member " . $member->getMemberId() . "\n";
            }
        }

        echo "Generated  " . $i . " reminders\n";
    }

}
