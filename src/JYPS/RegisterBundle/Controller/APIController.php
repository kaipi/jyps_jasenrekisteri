<?php
// /src/JYPS/RegisterBundle/Controller/APIController.php
namespace JYPS\RegisterBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

class APIController extends FOSRestController {
	/**
	 *@Rest\View
	 */
	public function getMembersAction() {
		$repository = $this->getDoctrine()
		                   ->getRepository('JYPSRegisterBundle:Member');

		$query = $repository->createQueryBuilder('m')
		                    ->where('m.membership_end_date >= :current_date')
		                    ->setParameter('current_date', new \DateTime("now"))
		                    ->orderBy('m.surname', 'ASC')
		                    ->getQuery();

		$members = $query->getResult();
		return $members;

	}

}
