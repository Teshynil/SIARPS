<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionType extends ChoiceType {

    public function configureOptions(OptionsResolver $resolver) {

        parent::configureOptions($resolver);
        $resolver->setDefault('choices', [
            'Lectura + Escritura + Bloqueo' => 07,
            'Lectura + Escritura' => 06,
            'Lectura + Bloqueo' => 05,
            'Escritura + Bloqueo' => 03,
            'Lectura' => 04,
            'Escritura' => 02,
            'Bloqueo' => 01,
            'Sin Permisos' => 00
        ]);
    }

}
