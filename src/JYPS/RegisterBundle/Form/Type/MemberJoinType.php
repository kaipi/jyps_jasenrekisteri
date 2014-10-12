<?php
//src/JYPS/RegisterBundle/Form/Type/MemberJoinType.php

namespace JYPS\RegisterBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use JYPS\RegisterBundle\Entity\IntrestConfig;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MemberJoinType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $conf = $options['intrest_configs'];
        $fee_conf = $options['memberfee_configs'];

        $builder
        ->add('firstname','text', array('attr'=> array('size'=> '63')))
        ->add('second_name','text',array('required'=>false,
                                         'attr'=> array('size'=> '63')))
        ->add('surname','text', array('attr'=> array('size'=> '63')))
        ->add('birth_year','text', array('attr' =>  array('size' => '4',
                                                          'max_length' => '4')))

        ->add('membertype','entity', array('class'=>'JYPS\RegisterBundle\Entity\MemberFeeConfig',
                                           'query_builder' => function(EntityRepository $fee_conf) {
                                                return $fee_conf->createQueryBuilder('f')
                                                ->where('f.show_on_join_form = 1')
                                                ->orderBy('f.member_type', 'ASC');},
                                                'property'=>'NameWithFeeAmount'))
        ->add('street_address','text',array('attr'=> array('size'=> '63')))
        ->add('postal_code','text', array('attr'=> array('size'=> '63')))
        ->add('city','text', array('attr'=> array('size'=> '63')))
        ->add('country','text',array('required'=>false,
                                     'attr'=> array('size'=> '63')))
        ->add('email', 'text', array('required'=>false,
                                     'attr'=> array('size'=> '63')))
        ->add('telephone','text', array('required'=>false,
                                        'attr'=> array('size'=> '63')))
        ->add('magazine_preference','checkbox',array('required'=>false,
                                                     'attr'=> array('size'=> '63')))
        ->add('mailing_list_yleinen','checkbox',array('required'=>false,
                                                      'attr'=> array('size'=> '63')))
        ->add('gender','choice', array('choices' => array( 
                                        true  => 'Mies', 
                                        false => 'Nainen'), 
                                       'required' => true,  
                                       'expanded' => false, 
                                       'multiple' => false, ))
        ->add('intrests', 'entity', array('class' => 'JYPS\RegisterBundle\Entity\IntrestConfig',
                                          'query_builder' => function(EntityRepository $conf) {
                                                return $conf->createQueryBuilder('c')
                                                ->orderBy('c.order', 'ASC'); 
                                            },
                                            'property'=>'intrestname',
                                            'multiple'=>true,
                                            'expanded'=>true,
                                            'mapped' => false,
                                            'required' => false,
                                            'property_path' => 'JYPS\RegisterBundle\Entity\Intrest',
                                            'attr' => array('size' => '21')))
    
        ->add('join_form_freeword', 'textarea',array('required' => false,
                                                     'attr' =>  array('cols' => '45', 'rows' => '10')))
        ->add('referer_person_name', 'text', array('required'=>false,
                                                    'attr' =>  array('size' => '63')))
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