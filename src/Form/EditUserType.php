<?php

namespace App\Form;

use App\Form\Requests\CreateUserRequest;
use App\Form\Requests\EditUserRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username', TextType::class, [
                    'help' => 'Este es el id de inicio de sesion.',
                    'label' => 'Nombre de usuario',
                    'attr' => ['autocomplete' => 'off']
                ])
                ->add('firstName', TextType::class, [
                    'label' => 'Nombre(s)',
                    'attr' => ['autocomplete' => 'off']
                ])
                ->add('lastName', TextType::class, [
                    'label' => 'Apellido(s)',
                    'required' => false,
                    'attr' => ['autocomplete' => 'off']
                ])
                ->add('email', EmailType::class, [
                    'label' => 'Correo Electronico',
                    'attr' => ['autocomplete' => 'off']
                ])
                ->add('loginMode', ChoiceType::class, [
                    'help' => 'Cambiar el modo de autentificación de Local al Directorio activo requiere asignar el nombre distinguido.',
                    'label' => 'Modo de autentificación',
                    'choices' => [
                        'Local'=>'local',
                        'Directorio Activo'=>'ldap'
                    ],
                    'attr' => ['autocomplete' => 'off',]
                    
                ])
                ->add('dn', TextType::class, [
                    'label' => 'Nombre Distinguido del Directorio Activo',
                    'required'=>false,
                    'attr' => ['autocomplete' => 'off',]
                ])
                ->add('photo', FileType::class, [
                    'label' => 'Imagen de Usuario',
                    'required' => false,
                    'attr' => ['placeholder' => 'Seleccionar una imagen']
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required'=>false,
                    'first_options' => ['label' => 'Contraseña'],
                    'second_options' => ['label' => 'Repetir Contraseña'],
                    'options' => ['attr' => ['autocomplete' => 'off']]
                ])
                ->add('properties', EditPropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user'],
                    'locked'=> $options['locked']
                ])
                ->add('submit', SubmitType::class, ['label' => 'Guardar cambios'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EditUserRequest::class,
            'em' => null,
            'user' => null,
            'locked' => false
        ]);
    }

}
