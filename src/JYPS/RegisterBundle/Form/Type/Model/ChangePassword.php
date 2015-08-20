<?php
//src/JYPS/RegisterBundle/Form/Type/Model/ChangePassword.php

namespace JYPS\RegisterBundle\Form\Type\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Vanha salasana väärin!"
     * )
     */
     protected $oldPassword;

    /**
     * @Assert\Length(
     *     min = 8,
     *     minMessage = "Salasanan minimipituus on 8 merkkiä!"
     * )
     */
     protected $newPassword;

     public function getOldPassword()
     {
        return $this->oldPassword;
     }
     public function getNewPassword()
     {
        return $this->newPassword;
     }
     public function setOldPassword($oldpassword)
     {
         $this->oldPassword = $oldpassword;
         return $this;
     }
     public function setNewPassword($newpassword)
     {
         $this->newPassword = $newpassword;
         return $this;
     }
}
