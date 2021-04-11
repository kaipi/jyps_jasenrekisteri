<?php
/**
 * Task processor
 * src/JYPS/RegisterBundle/Command/SendMemberFeesCommand.php */
namespace JYPS\RegisterBundle\Command;

use JYPS\RegisterBundle\Controller\MemberCardGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMemberCardsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('generate_membercards')
            ->setDescription('Generate membercards');

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
            ->where('m.membership_end_date >= :current_date')
            ->setParameter('current_date', new \DateTime("now"))
            ->getQuery();

        $members = $query->getResult();

        foreach ($members as $member) {
            echo $member->getEmail() . "\n";
            $this->makeMemberCard($member);
        }

    }
}
