<?php
namespace JYPS\RegisterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('oldPassword', 'password');
        $builder->add('newPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'Tarkista vanha salasana.',
            'required' => true,
            'first_options'  => array('label' => 'Uusi salasana'),
            'second_options' => array('label' => 'Toista uusi salasana'),

        ))
           ->add('save','submit');;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'JYPS\RegisterBundle\Form\Type\Model\ChangePassword',
        ));
    }

    public function getName()
    {
        return 'change_passwd';
    }
}