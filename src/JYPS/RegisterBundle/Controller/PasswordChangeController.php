<?php
//src/JYPS/RegisterBundle/Controller/PasswordChangeController.php;
namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JYPS\RegisterBundle\Form\Type\ChangePasswordType;
use JYPS\RegisterBundle\Form\Type\Model\ChangePassword;

class PasswordChangeController extends Controller
{
    public function changePasswdAction(Request $request)
    {
      $changePasswordModel = new ChangePassword();
      $form = $this->createForm(new ChangePasswordType(), $changePasswordModel);

      $form->handleRequest($request);
      print $request;
      if ($form->isSubmitted() && $form->isValid()) {
            /*$em = $this->getDoctrine()->getManager();
            $entity->setPassword($this->password = password_hash($entity->getPassword(), PASSWORD_BCRYPT, array("cost" => 15)));
            $em->persist($entity);
            $em->flush();*/
          return $this->redirect($this->generateUrl('change_password'));
      }

      return $this->render('JYPSRegisterBundle:Admin:change_password.html.twig', array(
          'form' => $form->createView(),
      ));      
    }
}