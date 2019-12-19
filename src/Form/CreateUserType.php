<?php

namespace App\Form;

use App\Form\Requests\CreateUserRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUserType extends AbstractType {

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
                ->add('photo', FileType::class, [
                    'label' => 'Imagen de Usuario',
                    'required' => false,
                    'attr'=>['placeholder'=>'Seleccionar una imagen']
                    ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Contraseña'],
                    'second_options' => ['label' => 'Repetir Contraseña'],
                    'options' => ['attr' => ['autocomplete' => 'off']]
                ])
                ->add('properties', CreatePropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user'],
                    'owner'=>false,
                    'ownerPermissions'=>false
                ])
                ->add('submit', SubmitType::class, ['label' => 'Crear documento'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => CreateUserRequest::class,
            'em' => null,
            'user' => null,
        ]);
    }

}
