<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType {

    private $em;

    public function __construct(EntityManagerInterface $em) {
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
                ->add('guestGroup', EntityType::class, ['label' => 'Grupo asignado a los usuarios automaticamente',
                    'help' => 'Solo aplica cuando la configuracion de grupos es Interna.',
                    'choice_label' => 'name',
                    'class' => Group::class])
                ->add('ldapGroupConfig', ChoiceType::class, ['label' => 'Como se accede a los grupos del Directorio Activo',
                    'choices' => [
                        'Usar grupos con un prefijo' => 'PREFIX',
                        'Usar Unidades Organizacionales como grupos' => 'OU',
                    ]
                ])
                
                ->add('ldapHost', TextType::class, ['label' => 'Host del Directorio Activo',
                    ])
                ->add('ldapEncryption', ChoiceType::class, ['label' => 'Encriptado de la conexión con el Directorio Activo',
                    'choices' => [
                        'TLS' => 'tls',
                        'SSL' => 'ssl',
                        'No usar encripción' => 'none'
                    ]
                ])
                ->add('ldapPort', TextType::class, ['label' => 'Puerto de conexión del Directorio Activo',
                    ])
                ->add('ldapReadUser', TextType::class, ['label' => 'DN del usuario de busqueda del Directorio Activo',
                    ])
                ->add('ldapReadUserPassword', TextType::class, ['label' => 'Contraseña del usuario de busqueda del Directorio Activo',
                    ])
                ->add('ldapBaseDN', TextType::class, ['label' => 'DN base para la busqueda dentro del Directorio Activo',
                    ])
                ->add('ldapEmailAttr', TextType::class, ['label' => 'Atributo usado para obtener el email',
                    ])
                ->add('ldapFirstNameAttr', TextType::class, ['label' => 'Atributo usado para obtener el(los) nombre(s)',
                    ])
                ->add('ldapLoginAttr', TextType::class, ['label' => 'Atributo usado para buscar al usuario',
                    ])
                ->add('ldapLastNameAttr', TextType::class, ['label' => 'Atributo usado para obtener el(los) apellido(s)',
                    ])
                
                ->add('ldapOwnerGroupDN', TextType::class, ['label' => 'DN del grupo asignado a los lideres de equipo',
                    'help' => 'Solo aplica cuando la configuracion de grupos es via Directorio activo.',
                    ])
                ->add('ldapAdminGroupDN', TextType::class, ['label' => 'DN del grupo de administradores',
                    'help' => 'Solo aplica cuando la configuracion de grupos es via Directorio activo.',
                    ])
                ->add('ldapUserGroupDN', TextType::class, ['label' => 'DN del grupo de usuarios',
                    'help' => 'Solo aplica cuando la configuracion de grupos es via Directorio activo.',
                    ])
                ->add('ldapOwnerGroupDN', TextType::class, ['label' => 'DN del grupo asignado a los lideres de equipo',
                    'help' => 'Solo aplica cuando la configuracion de grupos es via Directorio activo.',
                    ])
                ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $settings=$this->em->getRepository(Setting::class);
        $resolver->setDefaults([
            'data' => [
                'adminGroup' => $settings->getValue('adminGroup'),
                'groupConfig' => $settings->getValue('groupConfig'),
                'ldapGroupConfig' => $settings->getValue('ldapGroupConfig'),
                'guestGroup' => $settings->getValue('guestGroup'),
                'ldapHost' => $settings->getValue('ldapHost'),
                'ldapEncryption' => $settings->getValue('ldapEncryption'),
                'ldapPort' => $settings->getValue('ldapPort'),
                'ldapReadUser' => $settings->getValue('ldapReadUser'),
                'ldapReadUserPassword' => $settings->getValue('ldapReadUserPassword'),
                'ldapBaseDN' => $settings->getValue('ldapBaseDN'),
                'ldapEmailAttr' => $settings->getValue('ldapEmailAttr'),
                'ldapFirstNameAttr' => $settings->getValue('ldapFirstNameAttr'),
                'ldapLoginAttr' => $settings->getValue('ldapLoginAttr'),
                'ldapLastNameAttr' => $settings->getValue('ldapLastNameAttr'),
                'ldapOwnerGroupDN' => $settings->getValue('ldapOwnerGroupDN'),
                'ldapAdminGroupDN' => $settings->getValue('ldapAdminGroupDN'),
                'ldapUserGroupDN' => $settings->getValue('ldapUserGroupDN'),
                'ldapOwnerGroupDN' => $settings->getValue('ldapOwnerGroupDN')
            ]
        ]);
    }

}
