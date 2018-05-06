<?php
//src/JYPS/RegisterBundle/Form/Type/MemberAddType.php

namespace JYPS\RegisterBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MemberAddType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$conf = $options['intrest_configs'];

		$builder
			->add('firstname', TextType::class)
			->add('second_name', TextType::class, array('required' => false))
			->add('surname', TextType::class)
			->add('birth_year', TextType::class)
			->add('membertype', EntityType::class, array('class' => 'JYPS\RegisterBundle\Entity\MemberFeeConfig',
				'choice_label' => 'NameWithFeeAmount'))
			->add('street_address')
			->add('postal_code')
			->add('city')
			->add('country', TextType::class, array('required' => false,
				'data' => 'Suomi'))
			->add('email', EmailType::class, array('required' => false,
				'attr' => array('size' => '46')))
			->add('telephone', TextType::class, array('required' => false))
			->add('mailing_list_yleinen', CheckboxType::class, array('required' => false))
			->add('gender', ChoiceType::class, array('choices' => array(
				"Mies" => true,
				"Nainen" => false),
				'required' => true,
				'expanded' => false,
				'multiple' => false))
			->add('intrests', EntityType::class, array('class' => 'JYPS\RegisterBundle\Entity\IntrestConfig',
				'query_builder' => function (EntityRepository $conf) {
					return $conf->createQueryBuilder('c')
					->orderBy('c.intrestname', 'ASC');
				},
				'choice_label' => 'intrestname',
				'multiple' => true,
				'mapped' => false,
				'required' => false,
				'property_path' => 'JYPS\RegisterBundle\Entity\Intrest'))

			->add('join_form_freeword', TextareaType::class, array('required' => false))
			->add('referer_person_name', TextType::class, array('required' => false))
			->add('mark_fee_paid', CheckboxType::class, array('required' => false,
				'mapped' => false))
			->add('save', SubmitType::class);

	}
	public function getDefaultOptions(array $options) {
		return array('data_class' => 'JYPS\RegisterBundleBundle\Entity\Member',
		);
	}
	public function configureOptions(OptionsResolver $resolver) {
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
