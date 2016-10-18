<?php

namespace ISC\PlatformBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiviteImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', array(
                'required' => false,
            ))
            ->add('editFile', 'checkbox', array(
                'label'    => 'Modifier l\'image avant enregistrement ?',
                'required' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ISC\PlatformBundle\Entity\ActiviteImage'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'isc_platformbundle_activiteimage';
    }
}
