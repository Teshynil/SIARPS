<?php

namespace App\Form;

use App\Form\Requests\CreateVersionRequest;
use App\Form\Requests\EditVersionRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditVersionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('properties', EditPropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user']
                ])
                ->add('submit', SubmitType::class, ['label' => 'Editar Version'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EditVersionRequest::class,
            'em' => null,
            'user' => null,
        ]);
    }

}
