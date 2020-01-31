<?php

namespace App\Form;

use App\Form\Requests\CreateProjectRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProjectType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class, ['label' => 'Nombre'])
                ->add('description', TextareaType::class, ['label' => 'Descripción'])
                ->add('properties', CreatePropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user']
                ])
                ->add('documents', CollectionType::class, ['entry_type' => CreateDocumentType::class,
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => false,
                    'entry_options' => [
                        'label' => false,
                        'em'=>$options['em'],
                        'user'=>$options['user']
                    ],
                    'attr' => [
                        'class' => "symfony-collection",
                    ],
                ])
                ->add('submit', SubmitType::class, ['label' => 'Crear Projecto'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => CreateProjectRequest::class,
            'em' => null,
            'user' => null,
        ]);
    }

}
