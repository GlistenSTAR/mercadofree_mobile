<?php

namespace PublicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RetirarFondoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',TextType::class,array(
            	'label' => 'Email',
	            'attr' => ['class' => 'form-control']
            ))
	        ->add('monto',NumberType::class,array(
		        'label' => 'Monto',
		        'attr' => array(
			        'class' => 'form-control'
		        )
			));
    }
}
