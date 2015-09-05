<?php

namespace JYPS\RegisterBundle\Controller;

use JYPS\RegisterBundle\Entity\IntrestConfig;
use JYPS\RegisterBundle\Form\IntrestConfigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * IntrestConfig controller.
 *
 */
class IntrestConfigController extends Controller {

	/**
	 * Lists all IntrestConfig entities.
	 *
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository('JYPSRegisterBundle:IntrestConfig')->findAll();

		return $this->render('JYPSRegisterBundle:IntrestConfig:index.html.twig', array(
			'entities' => $entities,
		));
	}
	/**
	 * Creates a new IntrestConfig entity.
	 *
	 */
	public function createAction(Request $request) {
		$entity = new IntrestConfig();
		$form = $this->createCreateForm($entity);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($entity);
			$em->flush();

			return $this->redirect($this->generateUrl('member_intrestconfig_show', array('id' => $entity->getId())));
		}

		return $this->render('JYPSRegisterBundle:IntrestConfig:new.html.twig', array(
			'entity' => $entity,
			'form' => $form->createView(),
		));
	}

	/**
	 * Creates a form to create a IntrestConfig entity.
	 *
	 * @param IntrestConfig $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm(IntrestConfig $entity) {
		$form = $this->createForm(new IntrestConfigType(), $entity, array(
			'action' => $this->generateUrl('member_intrestconfig_create'),
			'method' => 'POST',
		));

		$form->add('submit', 'submit', array('label' => 'Create'));

		return $form;
	}

	/**
	 * Displays a form to create a new IntrestConfig entity.
	 *
	 */
	public function newAction() {
		$entity = new IntrestConfig();
		$form = $this->createCreateForm($entity);

		return $this->render('JYPSRegisterBundle:IntrestConfig:new.html.twig', array(
			'entity' => $entity,
			'form' => $form->createView(),
		));
	}

	/**
	 * Finds and displays a IntrestConfig entity.
	 *
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('JYPSRegisterBundle:IntrestConfig')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find IntrestConfig entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->render('JYPSRegisterBundle:IntrestConfig:show.html.twig', array(
			'entity' => $entity,
			'delete_form' => $deleteForm->createView()));
	}

	/**
	 * Displays a form to edit an existing IntrestConfig entity.
	 *
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('JYPSRegisterBundle:IntrestConfig')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find IntrestConfig entity.');
		}

		$editForm = $this->createEditForm($entity);
		$deleteForm = $this->createDeleteForm($id);

		return $this->render('JYPSRegisterBundle:IntrestConfig:edit.html.twig', array(
			'entity' => $entity,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		));
	}

	/**
	 * Creates a form to edit a IntrestConfig entity.
	 *
	 * @param IntrestConfig $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm(IntrestConfig $entity) {
		$form = $this->createForm(new IntrestConfigType(), $entity, array(
			'action' => $this->generateUrl('member_intrestconfig_update', array('id' => $entity->getId())),
			'method' => 'PUT',
		));

		$form->add('submit', 'submit', array('label' => 'Update'));

		return $form;
	}
	/**
	 * Edits an existing IntrestConfig entity.
	 *
	 */
	public function updateAction(Request $request, $id) {
		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository('JYPSRegisterBundle:IntrestConfig')->find($id);

		if (!$entity) {
			throw $this->createNotFoundException('Unable to find IntrestConfig entity.');
		}

		$deleteForm = $this->createDeleteForm($id);
		$editForm = $this->createEditForm($entity);
		$editForm->handleRequest($request);

		if ($editForm->isValid()) {
			$em->flush();

			return $this->redirect($this->generateUrl('member_intrestconfig_edit', array('id' => $id)));
		}

		return $this->render('JYPSRegisterBundle:IntrestConfig:edit.html.twig', array(
			'entity' => $entity,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		));
	}
	/**
	 * Deletes a IntrestConfig entity.
	 *
	 */
	public function deleteAction(Request $request, $id) {
		$form = $this->createDeleteForm($id);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$entity = $em->getRepository('JYPSRegisterBundle:IntrestConfig')->find($id);

			if (!$entity) {
				throw $this->createNotFoundException('Unable to find IntrestConfig entity.');
			}

			$em->remove($entity);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('member_intrestconfig'));
	}

	/**
	 * Creates a form to delete a IntrestConfig entity by id.
	 *
	 * @param mixed $id The entity id
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm($id) {
		return $this->createFormBuilder()
			->setAction($this->generateUrl('member_intrestconfig_delete', array('id' => $id)))
			->setMethod('DELETE')
			->add('submit', 'submit', array('label' => 'Delete'))
			->getForm()
		;
	}
}
