<?php

namespace App\Form;

use App\Entity\Group;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType {

    private $em;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('adminGroup', EntityType::class, ['label' => 'Grupo de Administración',
                    'choice_label' => 'name',
                    'class' => Group::class])
                ->add('groupConfig', ChoiceType::class, ['label' => 'Como se usan los grupos',
                    'choices' => [
                        'Usar grupos internos' => 'INTERNAL',
                        'Usar conexion con el Directorio Activo' => 'LDAP'
                    ]
                ])
                ->add('ldapGroupConfig', ChoiceType::class, ['label' => 'Como se accede a los grupos del Directorio Activo',
                    'choices' => [
                        'Usar grupos con un prefijo' => 'PREFIX',
                        'Usar Unidades Organizacionales como grupos' => 'OU',
                    ]
                ])
                ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data' => [
                'adminGroup' => $this->em->getRepository(\App\Entity\Setting::class)->getValue('adminGroup'),
                'groupConfig' => $this->em->getRepository(\App\Entity\Setting::class)->getValue('groupConfig'),
                'ldapGroupConfig' => $this->em->getRepository(\App\Entity\Setting::class)->getValue('ldapGroupConfig')
            ]
        ]);
    }

}
