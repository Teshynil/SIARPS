<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldSettingType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('label', TextType::class, ['label' => 'Etiqueta'])
                ->add('required', CheckboxType::class, ['label' => 'Obligatorio'])
                ->add('choices', TextareaType::class, ['label' => 'Opciones'])
                ->add('placeholder', TextType::class, ['label' => 'Placeholder'])
                ->add('multiple', CheckboxType::class, ['label' => 'Multiple'])
                ->add('constraints', TextareaType::class, ['label' => 'Condiciones',
                    'attr' => [
                        'class' => 'code-editor',
                        'data-code-type'=>'yaml'
                    ]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
                // Configure your form options here
        ]);
    }

}
