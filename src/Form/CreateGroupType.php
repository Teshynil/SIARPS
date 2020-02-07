<?php

namespace App\Form;

use App\Form\Requests\CreateGroupRequest;
use App\Form\Requests\CreateUserRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateGroupType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class, [
                    'label' => 'Nombre del grupo',
                    'attr' => ['autocomplete' => 'off']
                ])
                ->add('description', TextType::class, [
                    'label' => 'Descripción',
                ])
                ->add('fromActiveDirectory', CheckboxType::class, [
                    'label' => 'Grupo desde el directorio activo',
                    'required'=>false,
                ])
                ->add('dn', TextType::class, [
                    'label' => 'Ruta de acceso desde el directorio activo',
                    'required'=>false,
                ])
                ->add('properties', CreatePropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user'],
                    'group' => false,
                ])
                ->add('submit', SubmitType::class, ['label' => 'Crear Grupo'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => CreateGroupRequest::class,
            'em' => null,
            'user' => null,
        ]);
    }

}
