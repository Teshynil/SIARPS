<?php

namespace App\Form;

use App\Entity\Setting;
use App\Entity\Template;
use App\Form\Requests\CreateDocumentRequest;
use App\Form\Requests\EditDocumentRequest;
use App\Form\Requests\EditGroupRequest;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditGroupType extends AbstractType {

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
                ])
                ->add('submit', SubmitType::class, ['label' => 'Editar Grupo'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EditGroupRequest::class,
            'em' => null,
            'user' => null,
            'locked' => null,
        ]);
    }

}
