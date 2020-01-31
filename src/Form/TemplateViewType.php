<?php

namespace App\Form;

use App\Form\Requests\EditTemplateViewRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateViewType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('size', ChoiceType::class, ['label' => 'Tamaño de la hoja',
                    'choices' => [
                        'Carta' => 'letter',
                        'Oficio' => 'legal',
                        'Tabloide' => 'ledger',
                        'A5' => 'A5',
                        'A4' => 'A4',
                        'A3' => 'A3',
                        'B5' => 'B5',
                        'B4' => 'B4',
                        'JIS-B5' => 'JIS-B5',
                        'JIS-B4' => 'JIS-B4'
                    ]
                ])
                ->add('orientation', ChoiceType::class, ['label' => 'Orientación de la hoja',
                    'choices' => [
                        'Vertical' => 'portrait',
                        'Horizontal' => 'landscape'
                    ]
                ])
                ->add($builder->create('margin', FormType::class, [
                            'by_reference' => false,
                            'label' => 'Margenes',
                            'inherit_data' => true,
                            'data_class' => $options['data_class']
                        ])
                        ->add('top', NumberType::class, ['label' => 'Margen superior'])
                        ->add('header', NumberType::class, ['label' => 'Tamaño de la cabecera'])
                        ->add('left', NumberType::class, ['label' => 'Margen izquierdo'])
                        ->add('right', NumberType::class, ['label' => 'Margen derecho'])
                        ->add('bottom', NumberType::class, ['label' => 'Margen inferior'])
                        ->add('footer', NumberType::class, ['label' => 'Tamaño del pie de pagina'])
                )
                ->add($builder->create('template', FormType::class, [
                            'by_reference' => false,
                            'label' => 'Plantilla',
                            'inherit_data' => true,
                            'data_class' => $options['data_class']
                        ])
                        ->add('templateFromWord', FileType::class, ['label' => 'Leer desde Word',
                            'required'=>false,
                            'help'=>'Para enviar la plantilla desde word es necesario guardarlo como html y comprimir en un archivo Zip el archivo y la carpeta generada.'
                        ])
                        ->add('templateExternal', TextareaType::class, ['label' => 'Cabecera y Pie de pagina',
                            'attr' => [
                                'class' => 'code-editor'
                            ]
                        ])
                        ->add('templateBody', TextareaType::class, ['label' => 'Cuerpo',
                            'attr' => [
                                'class' => 'code-editor'
                            ]
                        ])
                )
                ->add('updateView', SubmitType::class, ['label' => 'Actualizar vista'])
                ->add('saveView', SubmitType::class, ['label' => 'Guardar'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EditTemplateViewRequest::class,
        ]);
    }

}
