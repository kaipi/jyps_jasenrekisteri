<?php
// /src/JYPS/RegisterBundle/Controller/APIController.php
namespace JYPS\RegisterBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

class APIController extends FOSRestController
{

    /**
     *@Rest\View
     */
    public function getMemberStatisticsAction($year)
    {
        $repository = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:Member');
        $query = $repository->createQueryBuilder('m')
            ->where('m.membership_start_date <= :year_end and m.membership_end_date > :year_end')
            ->select('count(m.id)')
            ->setParameter('year_end', new \DateTime("now"))
            ->groupBy('m.gender')
            ->getQuery();

        $result = $query->getScalarResult();
        return $this->handleView($this->view($result));
    }

}
