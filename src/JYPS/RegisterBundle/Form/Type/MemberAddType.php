<?php
//src/JYPS/RegisterBundle/Form/Type/MemberAddType.php

namespace JYPS\RegisterBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberAddType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$conf = $options['intrest_configs'];

		$builder
			->add('firstname', 'text')
			->add('second_name', 'text', array('required' => false))
			->add('surname', 'text')
			->add('birth_year', 'text')
			->add('membertype', 'entity', array('class' => 'JYPS\RegisterBundle\Entity\MemberFeeConfig',
				'property' => 'NameWithFeeAmount'))
			->add('street_address')
			->add('postal_code')
			->add('city')
			->add('country', 'text', array('required' => false,
				'data' => 'Suomi'))
			->add('email', 'email', array('required' => false,
				'attr' => array('size' => '46')))
			->add('telephone', 'text', array('required' => false))
			->add('magazine_preference', 'checkbox', array('required' => false))
			->add('mailing_list_yleinen', 'checkbox', array('required' => false))
			->add('gender', 'choice', array('choices' => array(
				true => 'Mies',
				false => 'Nainen'),
				'required' => true,
				'expanded' => false,
				'multiple' => false))
			->add('intrests', 'entity', array('class' => 'JYPS\RegisterBundle\Entity\IntrestConfig',
				'query_builder' => function (EntityRepository $conf) {
					return $conf->createQueryBuilder('c')
					->orderBy('c.intrestname', 'ASC');
				},
				'property' => 'intrestname',
				'multiple' => true,
				'mapped' => false,
				'required' => false,
				'property_path' => 'JYPS\RegisterBundle\Entity\Intrest'))

			->add('join_form_freeword', 'textarea', array('required' => false))
			->add('referer_person_name', 'text', array('required' => false))
			->add('mark_fee_paid', 'checkbox', array('required' => false,
				'mapped' => false))
			->add('save', 'submit');

	}
	public function getDefaultOptions(array $options) {
		return array('data_class' => 'JYPS\RegisterBundleBundle\Entity\Member',
		);
	}
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'JYPS\RegisterBundle\Entity\Member',
			'intrest_configs' => null,
			'memberfee_configs' => null,
		));
	}
	public function getName() {
		return 'memberid';
	}
}
