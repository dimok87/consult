<?php

namespace Consult\Bundle\FrontendBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class DepartmentType extends AbstractType
{
    private $userId;
    
    public function __construct($id)
    {
      $this->userId = $id;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $this->userId;
        $builder->add('title', 'text');
        $builder->add('consultants', 'entity', array(
            'class' => 'ConsultBusinessBundle:User',
            'property' => 'name',
            'multiple' => true,
            'expanded' => true,
            'query_builder' => function(EntityRepository $er ) use($id) {
               return $er->createQueryBuilder('u')->where("u.parent = " . $id . " or u.id = " . $id);
                       
            },
        ));
    }


    public function getName()
    {
        return 'department';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Consult\Bundle\BusinessBundle\Entity\Department',
            'csrf_protection' => true,
        ));
    }
}