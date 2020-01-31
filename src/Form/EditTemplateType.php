<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Template;
use App\Entity\User;
use App\Form\Requests\EditTemplateRequest;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTemplateType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class, ['label' => 'Nombre'])
                ->add('type', ChoiceType::class, ['label' => 'Tipo de Plantilla',
                    'attr' => ['class' => 'type-field-collapser'],
                    'choices' => [
                        'Archivo' => 'File',
                        'Formulario' => 'Form'
                    ]
                ])
                ->add('properties', EditPropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user'],
                    'locked' => $options['locked']
                ])
                ->add('templateForm', CollectionType::class, ['entry_type' => FormFieldType::class,
                    'label' => '',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => false,
                    'entry_options' => [
                        'label' => false,
                    ],
                    'attr' => [
                        'class' => "symfony-collection table-collection",
                    ],
                ])
                ->add('submit', SubmitType::class, ['label' => 'Actualizar Plantilla'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EditTemplateRequest::class,
            'em' => null,
            'user' => null,
            'locked' => false,
        ]);
    }

}
