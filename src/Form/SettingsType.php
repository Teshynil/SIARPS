<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('adminGroup', EntityType::class, ['label' => 'Grupo de Administración',
                    'choice_label' => 'name',
                    'class' => Group::class])
                ->add('groupConfig', ChoiceType::class, ['label' => 'Como se usan los grupos',
                    'choices' => [
                        'Usar conexion con el Directorio Activo' => 'LDAP',
                        'Usar grupos internos' => 'INTERNAL'
                    ]
                ])
                ->add('ldapGroupConfig', ChoiceType::class, ['label' => 'Como se accede a los grupos del Directorio Activo',
                    'choices' => [
                        'Usar Unidades Organizacionales como grupos' => 'OU',
                        'Usar grupos con un prefijo' => 'PREFIX'
                    ]
                ])
                ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
                // Configure your form options here
        ]);
    }

}
