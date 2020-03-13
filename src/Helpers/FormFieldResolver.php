<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers;

use App\Entity\Project;
use App\Entity\User;
use App\Entity\Version;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
        'Texto simple' => 'Texto simple', //'text'
        'Texto largo' => 'Texto largo', //'textarea'
        'Texto enriquecido' => 'Texto enriquecido', //'wysiwyg'
        'Entero' => 'Entero', //'integer'
        'Numero' => 'Numero', //'float'
        'Rango' => 'Rango', //'range'
        'Elección' => 'Elección', //'choice'
        'Fecha' => 'Fecha', //'date'
        'Fecha y Hora' => 'Fecha y Hora', //'datetime'
        'Imagen' => 'Imagen', //'image'
        'Archivo' => 'Archivo', //'file'
        'Enlace' => 'Enlace', //'link'
        'Integración' => 'Integración', //'integration'
    ];
    public const FIELDS_SYNTAX = [
        'Texto simple' => '{{@}}', //'text'
        'Texto largo' => '{{@}}', //'textarea'
        'Texto enriquecido' => '{{@|raw}}', //'wysiwyg'
        'Entero' => '{{@}}', //'integer'
        'Numero' => '{{@}}', //'float'
        'Rango' => '{{@|raw}}', //'range'
        'Elección' => '{{@}}', //'choice'
        'Fecha' => '{{@}}', //'date'
        'Fecha y Hora' => '{{@}}', //'datetime'
        'Imagen' => '{{@|raw}}', //'image'
        'Archivo' => '{{@|raw}}', //'file'
        'Enlace' => '{{@|raw}}', //'link'
        'Integración' => '{{TWIG}}', //'integration'
    ];

    public static function resolveFieldToView(array $field, EntityManagerInterface $em = null,Version $document) {
        $out = "";
        switch ($field['type']) {
            case 'Enlace':
                $out = '<a href="' . $field['value'] . '" target=_blank>' . $field['value'] . '</a>';
                break;
            case 'Rango':
                $out = '<div class="progress-group">
  <div class="progress-group-header">
    <div class="ml-auto font-weight-bold">'.$field['value'].'%</div>
  </div>
  <div class="progress-group-bars">
    <div class="progress progress-xs">
      <div class="progress-bar bg-primary" role="progressbar" style="width: '.$field['value'].'%" aria-valuenow="'.$field['value'].'" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>
</div>';
                break;
            case 'Integración':
                for ($i = 0; $i < count($field['value']); $i++) {
                    $field['value'][$i]=$em->getRepository(Project::class)->loadSnapshot($field['value'][$i],$document->getDate());
                }
                $out=$field['value'];
                break;
            default:
                $out = $field['value'];
                break;
        }
        return $out ?? "";
    }

    public static function resolveFieldToSyntax(array $field) {
        $out = "";
        if (isset(static::FIELDS_SYNTAX[$field['type']])) {
            $out = static::FIELDS_SYNTAX[$field['type']];
            $out = str_replace("@", $field['name'], $out);
        }
        return $out ?? "";
    }

    public static function resolveFieldToForm(array $field, FormBuilderInterface $form, User $user, EntityManager $em) {
        $options = [
            'label' => $field['settings']['label'] ?? $field['name'],
            'required' => $field['settings']['required'] ?? false,
            'attr' => [
                'placeholder' => $field['settings']['placeholder'] ?? ''
            ],
            'help' => $field['description'] ?? ""];
        switch ($field['type']) {
            case 'Entero':
                $formField = $form->create($field['name'], IntegerType::class, $options);
                break;
            case 'Numero':
                $formField = $form->create($field['name'], NumberType::class, $options);
                break;
            case 'Rango':
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
            case 'Elección':
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
            case 'Fecha':
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
            case 'Fecha y Hora':
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
            case 'Imagen':
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
            case 'Archivo':
                $formField = $form->create($field['name'], FileType::class, $options);
                break;
            case 'Texto enriquecido':
                $options = array_merge($options, [
                    'attr' => [
                        'class' => 'wysiwyg',
                    ]
                ]);
                $formField = $form->create($field['name'], TextAreaType::class, $options);
                break;
            case 'Texto largo':
                $formField = $form->create($field['name'], TextareaType::class, $options);
                break;
            case 'Integración':
                if ($user->getAdminMode()) {
                    $choices = $em->createQueryBuilder()
                                    ->select('p')
                                    ->from(Project::class, 'p')
                                    ->orderBy('p.name', 'ASC')
                                    ->indexBy('p', 'p.id')
                                    ->getQuery()->getResult();
                } else {
                    $choices = $em->createQueryBuilder()
                                    ->select('p')
                                    ->from(Project::class, 'p')
                                    ->where('p.group = ?1')
                                    ->orderBy('p.name', 'ASC')
                                    ->indexBy('p', 'p.id')
                                    ->setParameter(1, $user->getGroup()->getId())
                                    ->getQuery()->getResult();
                }
                $keychoices = array_keys($choices);

                $options = array_merge($options, [
                    'multiple' => true,
                    'required' => false,
                    'placeholder' => $field['settings']['placeholder'] ?? $options['label'],
                    'choices' => $keychoices,
                    'choice_value' => function ($key) use ($choices) {
                        return $key;
                    },
                    'choice_label' => function ($key) use ($choices) {
                        return $choices[$key]->getName();
                    },
                    'preferred_choices' => [],
                    'group_by' => function($key, $index) use ($choices) {
                        return $choices[$key]->getGroup()->getName();
                    },
                ]);
                $formField = $form->create($field['name'], ChoiceType::class, $options);

                break;
            default:
            case 'Enlace':
            case 'Texto simple':
                $formField = $form->create($field['name'], TextType::class, $options);
                break;
        }
        return $formField;
    }

}
