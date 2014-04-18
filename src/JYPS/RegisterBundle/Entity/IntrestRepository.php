<?php
// src/Acme/StoreBundle/Entity/IntrestRepository.php
namespace JYPS\RegisterBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InterestRepository extends EntityRepository
{
    public function findAllIntrests()
    {
        return   $intrests = $this->getDoctrine()
  				->getRepository("JYPSRegisterBundle:IntrestConfig")
  				->findAll();
    }
}