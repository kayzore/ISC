<?php

namespace ISC\PlatformBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use ISC\PlatformBundle\Form\Type\ActiviteImageType;

class ActiviteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('textActivity', 'textarea', array('required' => false))
            ->add('image', new ActiviteImageType())
            ->add('save',         'submit', array('label' => 'Ajouter !'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ISC\PlatformBundle\Entity\Activite',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'isc_platformbundle_activite';
    }
}
