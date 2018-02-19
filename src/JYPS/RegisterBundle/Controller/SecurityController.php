<?php
// src/JYPS/RegisterBundle/Controller/SecurityController.php;
namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\Security\Core\Security;

class SecurityController extends Controller {
	public function loginAction(Request $request) {
		$session = $request->getSession();

		// get the login error if there is one
		if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(
				SecurityContext::AUTHENTICATION_ERROR
			);
		} else {
			$error = $session->get(Security::AUTHENTICATION_ERROR);
			$session->remove(Security::AUTHENTICATION_ERROR);
		}

		return $this->render(
			'JYPSRegisterBundle:Security:login.html.twig',
			array(
				// last username entered by the user
				'last_username' => $session->get(Security::LAST_USERNAME),
				'error' => $error,
			)
		);
	}
}
