<?php

namespace Consult\Bundle\FrontendBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PasswordType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', 'repeated', array(
           'type' => 'password',
           'invalid_message' => 'Пароли должны совпадать',
        ));
    }

    public function getName()
    {
        return 'password';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Consult\Bundle\BusinessBundle\Entity\User',
            'csrf_protection' => false,
        ));
    }
}