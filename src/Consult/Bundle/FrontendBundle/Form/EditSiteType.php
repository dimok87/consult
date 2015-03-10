<?php

namespace Consult\Bundle\FrontendBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditSiteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('offlineEmail', 'text');
        $builder->add('offlineText', 'textarea');
        $builder->add('allowMark', 'checkbox');
    }


    public function getName()
    {
        return 'site';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Consult\Bundle\BusinessBundle\Entity\Site',
            'csrf_protection' => true,
        ));
    }
}