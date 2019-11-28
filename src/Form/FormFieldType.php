<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormFieldType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class, ['label' => 'Nombre'])
                ->add('description', TextareaType::class, ['label' => 'Descripción'])
                ->add('type', ChoiceType::class, ['label' => 'Tipo de campo',
                    'choices' => [
                        'Texto' => TextareaType::class,
                        'Entero' => IntegerType::class,
                        'Numero' => NumberType::class,
                        'Elección' => ChoiceType::class,
                        'Fecha' => DateType::class,
                        'Fecha y Hora' => DateTimeType::class,
                        'Imagen' => FileType::class,
                        'Archivo' => FileType::class,
                    ]
                ])
                ->add('required', CheckboxType::class, ['label' => 'Requerido'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
                // Configure your form options here
        ]);
    }

}
