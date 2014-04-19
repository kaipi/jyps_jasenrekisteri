<?php
//src/JYPS/RegisterBundle/Form/Type/MemberJoinType.php

namespace JYPS\RegisterBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JYPS\RegisterBundle\Entity\IntrestConfig;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MemberEditType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fee_conf = $options['memberfee_configs'];

        $builder
        ->add('firstname','text')
        ->add('surname','text')
        ->add('birth_year','text')
        ->add('membertype', 'entity', array('class' => 'JYPS\RegisterBundle\Entity\MemberFeeConfig',
                                            'query_builder' => function(EntityRepository $fee_conf) {
                                                  return $fee_conf->createQueryBuilder('f')
                                                   ->where('f.show_on_join_form = 1')
                                                   ->orderBy('f.id', 'ASC'); 
                                             },
                                            'property'=>'NameWithFeeAmount',
                                            'mapped' => false,
                                            'required' => true,
                                            'property_path' => 'JYPS\RegisterBundle\Entity\MemberFeeConfig'))
        ->add('street_address')
        ->add('postal_code')
        ->add('city')
        ->add('country','text',array('required'=>false,))
        ->add('email', 'text', array('required'=>false,))
        ->add('telephone','text', array('required'=>false,))
        ->add('magazine_preference','checkbox',array('required'=>false,))
        ->add('mailing_list_yleinen','checkbox',array('required'=>false,))
        ->add('invoice_preference','checkbox',array('required'=>false))
        ->add('memo','textarea',array('required'=>false))
        ->add('gender','choice', array('choices' => array( 
                                        true  => 'Mies', 
                                        false => 'Nainen'), 
                                       'required' => true,  
                                       'expanded' => false, 
                                       'multiple' => false, ))

        ->add('save', 'submit');

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
            'memberfee_configs' => null
        ));
    }
    public function getName()
    {
        return 'memberid';
    }
}