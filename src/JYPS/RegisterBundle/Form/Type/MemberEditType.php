<?php
//src/JYPS/RegisterBundle/Form/Type/MemberJoinType.php

namespace JYPS\RegisterBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberEditType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('firstname', TextType::class)
            ->add('second_name', TextType::class, array('required' => false))
            ->add('surname', TextType::class)
            ->add('birth_year', TextType::class)
            ->add('membertype', EntityType::class, array('class' => 'JYPS\RegisterBundle\Entity\MemberFeeConfig',
                'choice_name' => 'NameWithFeeAmount'))
            ->add('street_address')
            ->add('postal_code')
            ->add('city')
            ->add('country', TextType::class, array('required' => false))
            ->add('email', TextType::class, array('required' => false,
                'attr' => array('size' => '46')))
            ->add('telephone', TextType::class, array('required' => false))
            ->add('magazine_preference', CheckboxType::class, array('required' => false))
            ->add('mailing_list_yleinen', CheckboxType::class, array('required' => false))
            ->add('memo', TextareaType::class, array('required' => false))
            ->add('gender', ChoiceType::class, array('choices' => array(
                'Mies' => true,
                'Nainen' => false),
                'required' => true,
                'expanded' => false,
                'multiple' => false))
            ->add('next_memberfee_paid', CheckboxType::class, array('required' => false))
            ->add('membership_start_date', DateType::class, array('required' => false, 'widget' => 'single_text', 'format' => 'yyyy-MM-dd'))
            ->add('save', SubmitType::class);

    }
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'JYPS\RegisterBundleBundle\Entity\Member',
        );
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JYPS\RegisterBundle\Entity\Member',
            'intrest_configs' => null,
            'memberfee_configs' => null,
        ));
    }
    public function getName()
    {
        return 'memberid';
    }
}
