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

        $builder
        ->add('firstname','text')
        ->add('second_name','text', array('required'=>false))
        ->add('surname','text')
        ->add('birth_year','text')
        ->add('membertype','entity', array('class'=>'JYPS\RegisterBundle\Entity\MemberFeeConfig',
                                           'property'=>'NameWithFeeAmount'))
        ->add('street_address')
        ->add('postal_code')
        ->add('city')
        ->add('country','text',array('required'=>false,))
        ->add('email', 'text', array('required'=>false,
                                    'attr'=> array('size'=> '46')))
        ->add('telephone','text', array('required'=>false,))
        ->add('magazine_preference','checkbox',array('required'=>false,))
        ->add('mailing_list_yleinen','checkbox',array('required'=>false,))
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