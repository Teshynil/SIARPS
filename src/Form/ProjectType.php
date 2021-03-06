<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProjectType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $options['usr'];
        $em = $options['em'];
        $group = $user->getGroup();
        $builder
                ->add('name', TextType::class, ['label' => 'Nombre'])
                ->add('description', TextareaType::class, ['label' => 'Descripción'])
                ->add('owner', EntityType::class, ['label' => 'Dueño',
                    'class' => User::class,
                    'query_builder' => function (EntityRepository $er) use ($user, $group) {
                        if ($user->getAdminMode()) {
                            return $er->createQueryBuilder('u')
                                    ->orderBy('u.username', 'ASC');
                        } else {
                            return $er->createQueryBuilder('u')
                                    ->where('u.group = ?1')
                                    ->orderBy('u.username', 'ASC')
                                    ->setParameter(1, $group->getId());
                        }
                    },
                    'choice_label' => function ($user) {
                        return $user->getFullName() . ' - ' . $user->getGroup()->getName();
                    },
                    'preferred_choices' => [$user],
                    'group_by' => function($choice, $key, $value) {
                        return $choice->getGroup()->getName();
                    },
                ])
                ->add('group', EntityType::class, ['label' => 'Grupo',
                    'class' => Group::class,
                    'query_builder' => function (EntityRepository $er) use ($user, $group) {
                        if ($user->getAdminMode()) {
                            return $er->createQueryBuilder('g')
                                    ->orderBy('g.name', 'ASC');
                        } else {
                            return $er->createQueryBuilder('g')
                                    ->where('g.id = ?1')
                                    ->orderBy('g.name', 'ASC')
                                    ->setParameter(1, $group->getId());
                        }
                    },
                    'choice_label' => 'name',
                    'preferred_choices' => [$group],
                ])
                ->add('documents', CollectionType::class, ['entry_type' => DocumentType::class,
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => false,
                    'entry_options' => [
                        'label' => false,
                        'em'=>$options['em'],
                        'usr'=>$options['usr']
                    ],
                    'attr' => [
                        'class' => "symfony-collection",
                    ],
                ])
                ->add('ownerPermissions', PermissionType::class, ['label' => 'Permisos del dueño', 'preferred_choices' => [7]])
                ->add('groupPermissions', PermissionType::class, ['label' => 'Permisos del Grupo', 'preferred_choices' => [4]])
                ->add('otherPermissions', PermissionType::class, ['label' => 'Permisos de otros', 'preferred_choices' => [0]])
                ->add('submit', SubmitType::class, ['label' => 'Crear documento'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'em' => null,
            'usr' => null,
        ]);
    }

}
