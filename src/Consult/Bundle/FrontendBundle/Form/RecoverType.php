<?php

namespace Consult\Bundle\FrontendBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecoverType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'text');
    }


    public function getName()
    {
        return 'recover';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Consult\Bundle\BusinessBundle\Entity\Recover',
            'csrf_protection' => true,
        ));
    }
}