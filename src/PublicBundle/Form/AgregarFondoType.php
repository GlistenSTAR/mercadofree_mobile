<?php

namespace PublicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Description of AgregarFondoType
 *
 * @author Vadino
 */
class AgregarFondoType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Email:',
                    'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank(['message' => 'Email es requerido']),
                        new Email(['message' => 'Formato de email invalido']),
                    ],
                ])
            ->add(
                'monto',
                NumberType::class,
                [
                    'label' => 'Monto:',
                    'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank(['message' => 'Monto es requerido']),
                        new GreaterThan(['value' => 0, 'message' => 'Monto invalido']),
                    ],
                ])
            ->add('enviar', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'public_agregar_fondo';
    }
}
