<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;

class FormFieldResolver {

    public const FORM_FIELDS = [
        'Texto simple' => 'text',
        'Texto largo' => 'textarea',
        'Texto enriquecido' => 'wysiwyg',
        'Entero' => 'integer',
        'Numero' => 'float',
        'Elección' => 'choice',
        'Fecha' => 'date',
        'Fecha y Hora' => 'datetime',
        'Imagen' => 'image',
        'Archivo' => 'file',
        'Enlace' => 'link',
    ];

    public static function resolveFieldToView(array $field): string {
        $out="";
        switch ($field['type']){
            case 'link':
                $out='<a href="'.$field['value'].'" target=_blank>'.$field['value'].'</a>';
                break;
            default:
                $out=$field['value'];
                break;
        }
        return $out??"";
    }
    
    public static function resolveFieldToForm(array $field, FormBuilderInterface $form) {
        $options = [
            'label' => $field['settings']['label'] ?? $field['name'],
            'required' => $field['settings']['required'] ?? false,
            'attr' => [
                'placeholder' => $field['settings']['placeholder'] ?? ''
            ],
            'help' => $field['description'] ?? ""];
        switch ($field['type']) {
            case 'integer':
                $formField = $form->create($field['name'], IntegerType::class, $options);
                break;
            case 'float':
                $formField = $form->create($field['name'], NumberType::class, $options);
                break;
            case 'range':
                $options = array_merge($options, [
                    'attr' => [
                        'min' => $field['settings']['min'] ?? 0,
                        'max' => $field['settings']['max'] ?? 100,
                        'step' => $field['settings']['step'] ?? 1,
                        'class' => 'custom-range'
                    ]
                ]);
                $formField = $form->create($field['name'], RangeType::class, $options);
                break;
            case 'choice':
                $choicesValues = str_replace("\r", "", $field['settings']['choices']);
                $choicesValues = explode("\n", $choicesValues);
                $choices = array_combine($choicesValues, $choicesValues);
                $options = array_merge($options, [
                    'choices' => $choices ?? [],
                    'multiple' => $field['settings']['multiple'] ?? false,
                    'placeholder' => $field['settings']['placeholder'] ?? $options['label']
                ]);
                $formField = $form->create($field['name'], ChoiceType::class, $options);

                break;
            case 'date':
                $options = array_merge($options, [
                    'widget' => 'single_text',
                    'format' => 'DD/MM/YYYY',
                    'html5' => false,
                    'attr' => [
                        'class' => 'datetimepicker',
                        'data-type' => 'simple'
                    ]
                ]);
                $formField = $form->create($field['name'], DateType::class, $options);

                break;
            case 'datetime':
                $options = array_merge($options, [
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'DD/MM/YYYY HH:mm',
                    'attr' => [
                        'class' => 'datetimepicker',
                        'data-type' => 'simple',
                        'data-time' => 'time'
                    ]
                ]);
                $formField = $form->create($field['name'], DateTimeType::class, $options);
                break;
            case 'image':
                $options = array_merge($options, [
                    'attr' => [
                        'accept' => "image/*"
                    ],
                    'constraints' => [
                        new Image()
                    ],
                ]);
                $formField = $form->create($field['name'], FileType::class, $options);
                break;
            case 'file':
                $formField = $form->create($field['name'], FileType::class, $options);
                break;
            case 'wysiwyg':
                $options = array_merge($options, [
                    'attr' => [
                        'class' => 'quilljs',
                    ]
                ]);
                $formField = $form->create($field['name'], TextAreaType::class, $options);
                break;
            case 'textarea':
                $formField = $form->create($field['name'], TextareaType::class, $options);
                break;
            default:
            case 'link':
            case 'text':
                $formField = $form->create($field['name'], TextType::class, $options);
                break;
        }
        return $formField;
    }

}
