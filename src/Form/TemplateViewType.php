<?php

namespace App\Form;

use App\Form\Requests\EditTemplateViewRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                        'Carta' => ['name' => 'letter', 'height' => '27.94', 'width' => '21.59'],
                        'Oficio' => ['name' => 'legal', 'height' => '35.6', 'width' => '21.6'],
                        'Tabloide' => ['name' => 'ledger', 'height' => '43.2', 'width' => '27.9'],
                        'A5' => ['name' => 'A5', 'height' => '21.0', 'width' => '14.8'],
                        'A4' => ['name' => 'A4', 'height' => '29.7', 'width' => '21.0'],
                        'A3' => ['name' => 'A3', 'height' => '42.0', 'width' => '29.7'],
                        'B5' => ['name' => 'B5', 'height' => '25.0', 'width' => '17.6'],
                        'B4' => ['name' => 'B4', 'height' => '35.3', 'width' => '25.0'],
                        'JIS-B5' => ['name' => 'JIS-B5', 'height' => '25.7', 'width' => '18.2'],
                        'JIS-B4' => ['name' => 'JIS-B4', 'height' => '36.4', 'width' => '25.7']
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
                ->add('template', TextareaType::class, ['label' => false,
                    'attr' => [
                        'class' => 'code-editor'
                    ]
                ])
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
