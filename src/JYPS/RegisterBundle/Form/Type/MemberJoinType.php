<?php
//src/JYPS/RegisterBundle/Form/Type/MemberJoinType.php

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

class MemberJoinType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $conf = $options['intrest_configs'];
        $fee_conf = $options['memberfee_configs'];

        $builder
            ->add('firstname', TextType::class, array('attr' => array('size' => '63')))
            ->add('second_name', TextType::class, array('required' => false,
                'attr' => array('size' => '63')))
            ->add('surname', TextType::class, array('attr' => array('size' => '63')))
            ->add('birth_year', TextType::class, array('attr' => array('size' => '4',
                'max_length' => '4')))

            ->add('membertype', EntityType::class, array('class' => 'JYPS\RegisterBundle\Entity\MemberFeeConfig',
                'query_builder' => function (EntityRepository $fee_conf) {
                    return $fee_conf->createQueryBuilder('f')
                        ->where('f.show_on_join_form = 1')
                        ->orderBy('f.member_type', 'ASC');
                },
                'choice_label' => 'NameWithFeeAmount'))
            ->add('street_address', TextType::class, array('attr' => array('size' => '63')))
            ->add('postal_code', TextType::class, array('attr' => array('size' => '63')))
            ->add('city', TextType::class, array('attr' => array('size' => '63')))
            ->add('country', TextType::class, array('required' => false,
                'attr' => array('size' => '63')))
            ->add('email', EmailType::class, array('required' => true,
                'attr' => array('size' => '63')))
            ->add('telephone', TextType::class, array('required' => true,
                'attr' => array('size' => '63')))
            ->add('mailing_list_yleinen', CheckboxType::class, array('required' => false,
                'attr' => array('size' => '63')))
            ->add('gender', ChoiceType::class, array('choices' => array(
                'Mies' => true,
                'Nainen' => false),
                'required' => true,
                'expanded' => false,
                'multiple' => false))
            ->add('intrests', EntityType::class, array('class' => 'JYPS\RegisterBundle\Entity\IntrestConfig',
                'query_builder' => function (EntityRepository $conf) {
                    return $conf->createQueryBuilder('c')
                        ->orderBy('c.order', 'ASC');
                },
                'choice_label' => 'intrestname',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'required' => false,
                'property_path' => 'JYPS\RegisterBundle\Entity\Intrest',
                'attr' => array('size' => '21')))

            ->add('join_form_freeword', TextareaType::class, array('required' => false,
                'attr' => array('cols' => '45', 'rows' => '10')))
            ->add('referer_person_name', TextType::class, array('required' => false,
                'attr' => array('size' => '63')))
            ->add('save', SubmitType::class);
    }
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'JYPS\RegisterBundleBundle\Entity\Member',
        );
    }
    public function configureOptions(OptionsResolver $resolver)
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
