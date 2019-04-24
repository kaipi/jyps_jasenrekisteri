<?php
//src/JYPS/RegisterBundle/Form/Type/MemberFeeType.php

namespace JYPS\RegisterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberFeeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('memberid', 'integer')
            ->add('memberfee_amount', 'checkbox', array('required' => false))
            ->add('memo')
            ->add('paid', 'checkbox')
            ->add('save', 'submit');

    }
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'JYPS\RegisterBundleBundle\Entity\MemberFee',
        );
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null,

        ));
    }
    public function getName()
    {
        return 'memberfee';
    }
}
