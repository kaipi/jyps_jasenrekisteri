<?php
/**
 * Task processor
 * src/JYPS/RegisterBundle/Command/SendMemberFeesCommand.php */
namespace JYPS\RegisterBundle\Command;

use Egulias\EmailValidator\EmailValidator;
use JYPS\RegisterBundle\Controller\MemberCardGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMemberFeesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('send_memberfee_emails')
            ->setDescription('Send memberfee emails');

    }
    private function makeMemberCard($member)
    {

        $baseimage = $this->getContainer()->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/images/JYPS_Jasenkortti.png');
        $font = $this->getContainer()->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/fonts/LucidaGrande.ttf');
        $card_image = $this->getContainer()->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/savedCards/');

        return MemberCardGenerator::generateMembershipCard($member, $baseimage, $font, $card_image);
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $errors = 0;
        $sent = 0;
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $bankaccount = $this->getContainer()->get('doctrine')
            ->getRepository('JYPSRegisterBundle:SystemParameter')
            ->findOneBy(array('key' => 'BankAccount'));

        $repository = $this->getContainer()->get('doctrine')
            ->getRepository('JYPSRegisterBundle:Member');
        $query = $repository->createQueryBuilder('m')
            ->where('m.membership_end_date >= :current_date and m.membership_start_date <= :period_start')
            ->setParameter('current_date', new \DateTime("now"))
            ->setParameter('period_start', new \DateTime("first day of January " . date('Y')))
            ->getQuery();

        $members = $query->getResult();

        foreach ($members as $member) {
            echo $member->getEmail() . "\n";
            echo $this->makeMemberCard($member);

            $memberfee = $this->getContainer()->get('doctrine')
                ->getRepository('JYPSRegisterBundle:MemberFee')
                ->findOneBy(
                    array('member_id' => $member->getId(),
                        'fee_period' => date('Y'),
                        'email_sent' => null,
                        'paid' => 0)
                );

            if (empty($memberfee) || $member->getParent() !== null) {
                continue;
            }
            $validator = new EmailValidator();

            $errors = "";

            if (!is_null($member->getEmail()) && $member->getEmail() != "") {
                echo "email ok\n";
                echo $this->makeMemberCard($member);
                $message = new \Swift_Message();
                $message->setSubject("JYPS ry:n jäsenmaksu vuodelle " . date('Y'))
                    ->setFrom("jasenrekisteri@jyps.fi")
                    ->setTo(array($member->getEmail()))
                    ->attach(\Swift_Attachment::fromPath(
                        $this->makeMemberCard($member))
                    )
                    ->setBody($this->getContainer()
                            ->get('templating')
                            ->render(
                                'JYPSRegisterBundle:MemberFee:memberfee_email.txt.twig',
                                array('member' => $member,
                                    'memberfee' => $memberfee,
                                    'bankaccount' => $bankaccount,
                                    'virtualbarcode' => $memberfee->getVirtualBarcode($bankaccount),
                                    'year' => date("Y"))
                            )
                    );

                //attach also all childmembers cards to mail
                foreach ($member->getChildren() as $child) {
                    $message
                        ->attach(\Swift_Attachment::fromPath($this->makeMemberCard($child)));
                }
                // $this->getContainer()->get('mailer')->send($message);
                $memberfee->setEmailSent(1);
                $em->flush($memberfee);

                $sent++;
                if ($sent % 10 == 0) {
                    echo "Sleeping for a while... \n";
                    $transport = $this->getContainer()
                        ->get('mailer')->getTransport();
                    $spool = $transport->getSpool();
                    $spool->flushQueue(
                        $this->getContainer()
                            ->get('swiftmailer.transport.real')
                    );
                    sleep(3);
                }
            } else {
                $errors++;
            }
            echo $sent . "\n";
        }

    }
}
