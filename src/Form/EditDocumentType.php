<?php

namespace App\Form;

use App\Entity\Setting;
use App\Entity\Template;
use App\Form\Requests\CreateDocumentRequest;
use App\Form\Requests\EditDocumentRequest;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDocumentType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', TextType::class, ['label' => 'Nombre',
                    'attr' => [
                        'class' => "text-sync",
                        'data-target' => "#header-{{form.parent.vars.id}}",
                    ]
                ])
                ->add('properties', EditPropertiesType::class, [
                    'data_class' => $options['data_class'],
                    'em' => $options['em'],
                    'user' => $options['user']
                ])
                ->add('template', EntityType::class, ['label' => 'Plantilla',
                    'class' => Template::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        if ($options['user']->getAdminMode()) {
                            return $er->createQueryBuilder('t')
                                    ->orderBy('t.name', 'ASC');
                        } else {
                            return $er->createQueryBuilder('t')
                                    ->where('t.group = ?1')
                                    ->orWhere('t.group is null')
                                    ->orderBy('t.name', 'ASC')
                                    ->setParameter(1, $options['user']->getGroup()->getId());
                        }
                    },
                    'choice_label' => function ($template) {
                        return $template->getName();
                    },
                    'preferred_choices' => [$options['em']->getRepository(Setting::class)->getValue('nullTemplate')],
                    'group_by' => function($choice, $key, $value) {
                        $out = "Plantillas ";
                        if ($choice->getGroup() != null) {
                            $out .= "de " . $choice->getGroup()->getName();
                        } else {
                            $out .= "globales";
                        }
                        return $out;
                    },
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => EditDocumentRequest::class,
            'em' => null,
            'user' => null,
        ]);
    }

}
