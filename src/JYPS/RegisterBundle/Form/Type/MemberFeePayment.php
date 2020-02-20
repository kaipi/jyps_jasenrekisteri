<?php

//src/Form/Type/MemberFeePayment.php
namespace JYPS\RegisterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class MemberFeePayment extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('additional_target', ChoiceType::class, array('choices' => array(
                'Ei tukimaksua' => "EiTukimaksua",
                'Ohjatut lenkit' => "TUKILENKIT",
                'Fillariakatemia' => "TUKIAKATEMIA",
                "Pyöräilynedistäminen" => "TUKIEDISTAMINEN",
                "Pyöräilyliiton jäsenyys" => "TUKIPYORALIITTO",
                "Yleinen toiminta" => "TUKIYLEINEN"),
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'data' => 'EiTukimaksua',
                'placeholder' => false))
            ->add('additional_sum', ChoiceType::class, array('choices' => array(
                '0eur' => 0,
                '5eur' => 5,
                '10eur' => 10,
                '20eur' => 20,
                '50eur' => 50),
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'data' => 0,
                'placeholder' => false))
            ->add('save', SubmitType::class);
    }
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'JYPS\RegisterBundleBundle\Entity\MemberFee',
        );
    }
}
