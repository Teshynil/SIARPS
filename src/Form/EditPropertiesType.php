<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPropertiesType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if ($options['owner']) {
            $builder->add('owner', EntityType::class, ['label' => 'Dueño',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    if ($options['user']->getAdminMode()) {
                        return $er->createQueryBuilder('u')
                                        ->orderBy('u.username', 'ASC');
                    } else {
                        return $er->createQueryBuilder('u')
                                        ->where('u.group = ?1')
                                        ->orderBy('u.username', 'ASC')
                                        ->setParameter(1, $options['user']->getGroup()->getId());
                    }
                },
                'choice_label' => function ($user) {
                    return $user->getFullName() . ' - ' . $user->getGroup()->getName();
                },
                'preferred_choices' => [$options['user']],
                'group_by' => function($choice, $key, $value) {
                    return $choice->getGroup()->getName();
                },
            ]);
        }
        if ($options['group']) {
            $builder->add('group', EntityType::class, ['label' => 'Grupo',
                'class' => Group::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    if ($options['user']->getAdminMode()) {
                        return $er->createQueryBuilder('g')
                                        ->orderBy('g.name', 'ASC');
                    } else {
                        return $er->createQueryBuilder('g')
                                        ->where('g.id = ?1')
                                        ->orderBy('g.name', 'ASC')
                                        ->setParameter(1, $options['user']->getGroup()->getId());
                    }
                },
                'choice_label' => 'name',
                'placeholder' => $options['user']->getAdminMode() ? 'Sin grupo' : false,
                'required' => $options['user']->getAdminMode() ? false : true,
                'empty_data' => '',
                'preferred_choices' => [$options['user']->getGroup()],
            ]);
        }
        if ($options['ownerPermissions']) {
            $builder->add('ownerPermissions', PermissionType::class, ['label' => 'Permisos del dueño', 'preferred_choices' => [7]]);
        }
        if ($options['groupPermissions']) {
            $builder->add('groupPermissions', PermissionType::class, ['label' => 'Permisos del Grupo', 'preferred_choices' => [4]]);
        }
        if ($options['otherPermissions']) {
            $builder->add('otherPermissions', PermissionType::class, ['label' => 'Permisos de otros', 'preferred_choices' => [0]]);
        }
        if ($options['locked']) {
            $builder->add('locked', ChoiceType::class, [
                'label' => 'Entidad Bloqueada',
                'choices' => [
                    'Desbloqueada' => false,
                    'Bloqueada' => true
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'label' => false,
            'inherit_data' => true,
            'em' => null,
            'user' => null,
            'locked' => null,
            'owner' => true,
            'group' => true,
            'ownerPermissions' => true,
            'groupPermissions' => true,
            'otherPermissions' => true
        ]);
    }

}
