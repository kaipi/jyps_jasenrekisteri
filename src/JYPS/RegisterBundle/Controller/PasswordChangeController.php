<?php
//src/JYPS/RegisterBundle/Controller/PasswordChangeController.php;
namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JYPS\RegisterBundle\Form\Type\ChangePasswordType;
use JYPS\RegisterBundle\Form\Type\Model\ChangePassword;

class PasswordChangeController extends Controller
{

  public function indexAction(Request $request)
  {
    $changePasswordModel = new ChangePassword();
    $form = $this->createForm(new ChangePasswordType(), $changePasswordModel, array('action'=>$this->generateUrl('index_password'),
                                                                                    'method'=> 'POST'));
    if ($request->isMethod('POST')) {

      $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
              $user = $this->getUser();
              $em = $this->getDoctrine()->getManager();
              $user->setPassword(password_hash($changePasswordModel->getNewPassword(), PASSWORD_BCRYPT, array("cost" => 15)));
              $em->persist($user);
              $em->flush();
          return $this->redirect($this->generateUrl('index_password'));
        }
    }
    return $this->render('JYPSRegisterBundle:Admin:change_password.html.twig', array(
       'form' => $form->createView()));     
  }
}