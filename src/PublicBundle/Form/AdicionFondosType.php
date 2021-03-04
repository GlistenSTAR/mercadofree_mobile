<?php

namespace PublicBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacturaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('provincia', EntityType::class, array(
		        'class' => 'AdministracionBundle\Entity\Provincia',
		        'label' => 'Provincia',
		        'attr' => array(
			        'class' => 'form-control'
		        )
            ))
            ->add('nombre',TextType::class,array(
            	'label' => 'Nombre',
	            'attr' => array(
	            	'class' => 'form-control'
	            )
            ))
	        ->add('apellidos',TextType::class,array(
		        'label' => 'Apellidos',
		        'attr' => array(
			        'class' => 'form-control'
		        )
	        ))
	        ->add('dni',TextType::class,array(
		        'label' => 'DNI',
		        'attr' => array(
			        'class' => 'form-control'
		        )
	        ))
	        ->add('calle',TextType::class,array(
		        'label' => 'Calle',
		        'attr' => array(
			        'class' => 'form-control'
		        )
	        ))
	        ->add('numero',TextType::class,array(
		        'label' => 'Número',
		        'attr' => array(
			        'class' => 'form-control'
		        )
	        ))
	        ->add('pisoApto',TextType::class,array(
		        'label' => 'Piso o departamento',
		        'attr' => array(
			        'class' => 'form-control'
		        ),
		        'required' => false
	        ))
	        ->add('codPostal',TextType::class,array(
		        'label' => 'Código Postal',
		        'attr' => array(
			        'class' => 'form-control'
		        )
	        ))
	        ->add('localidad',TextType::class,array(
		        'label' => 'Localidad',
		        'attr' => array(
			        'class' => 'form-control'
		        )
	        ))
	        ->add('metodoPago',HiddenType::class,array(
		        'mapped' => false
	        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdministracionBundle\Entity\Factura'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'public_factura';
    }


}
