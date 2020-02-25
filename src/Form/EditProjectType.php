<?php

namespace App\Form;

use App\Entity\Document;
use App\Form\Requests\EditProjectRequest;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class, ['label' => 'Nombre'])
                ->add('description', TextareaType::class, ['label' => 'Descripción'])
                ->add('properties', EditPropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user'],
                    'locked' => $options['locked']
                ])
                ->add('summary', EntityType::class, ['label' => 'Documento de resumen',
                    'help'=>'Este documento representa la información minima que contiene un proyecto',
                    'class' => Document::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('d')
                                ->where('d.project = ?1')
                                ->orderBy('d.name', 'ASC')
                                ->setParameter(1, $options['project']->getId());
                    },
                    'choice_label' => function ($document) {
                        return $document->getName();
                    },
                ])
                ->add('progressDocument', EntityType::class, ['label' => 'Documento de progreso',
                    'help'=>'Cada version de este documento actualizara el progreso del proyecto. Debe tener un campo llamado "progress" ',
                    'class' => Document::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('d')
                                ->where('d.project = ?1')
                                ->orderBy('d.name', 'ASC')
                                ->setParameter(1, $options['project']->getId());
                    },
                    'choice_label' => function ($document) {
                        return $document->getName();
                    },
                ])
                ->add('documents', CollectionType::class, ['entry_type' => EditDocumentType::class,
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => false,
                    'entry_options' => [
                        'label' => false,
                        'em' => $options['em'],
                        'user' => $options['user']
                    ],
                    'attr' => [
                        'class' => "symfony-collection",
                    ],
                ])
                ->add('submit', SubmitType::class, ['label' => 'Editar Projecto'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EditProjectRequest::class,
            'em' => null,
            'user' => null,
            'project' => null,
            'locked' => false,
        ]);
    }

}
