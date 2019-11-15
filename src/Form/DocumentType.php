<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Setting;
use App\Entity\Template;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class DocumentType extends AbstractType {

    private $em;
    private $tk;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $ts) {
        $this->em = $em;
        $this->tk = $ts->getToken();
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $this->tk->getUser();
        $group = $user->getGroup();
        $builder
                ->add('name', TextType::class, ['label' => 'Nombre',
                    'attr'=>[
                        'class'=>"text-sync",
                        'data-target' => "#header-{{form.parent.vars.id}}",
                    ]
                    ])
                ->add('owner', EntityType::class, ['label' => 'Dueño',
                    'class' => User::class,
                    'query_builder' => function (EntityRepository $er) use ($user, $group) {
                        if ($user->getAdminMode()) {
                            return $er->createQueryBuilder('u')
                                    ->orderBy('u.username', 'ASC');
                        } else {
                            return $er->createQueryBuilder('u')
                                    ->where('u.group = ?1')
                                    ->orderBy('u.username', 'ASC')
                                    ->setParameter(1, $group->getId());
                        }
                    },
                    'choice_label' => function ($user) {
                        return $user->getFullName() . ' - ' . $user->getGroup()->getName();
                    },
                    'preferred_choices' => [$user],
                    'group_by' => function($choice, $key, $value) {
                        return $choice->getGroup()->getName();
                    },
                ])
                ->add('group', EntityType::class, ['label' => 'Grupo',
                    'class' => Group::class,
                    'query_builder' => function (EntityRepository $er) use ($user, $group) {
                        if ($user->getAdminMode()) {
                            return $er->createQueryBuilder('g')
                                    ->orderBy('g.name', 'ASC');
                        } else {
                            return $er->createQueryBuilder('g')
                                    ->where('g.id = ?1')
                                    ->orderBy('g.name', 'ASC')
                                    ->setParameter(1, $group->getId());
                        }
                    },
                    'choice_label' => 'name',
                    'preferred_choices' => [$group],
                ])
                ->add('template', EntityType::class, ['label' => 'Plantilla',
                    'class' => Template::class,
                    'query_builder' => function (EntityRepository $er) use ($user, $group) {
                        if ($user->getAdminMode()) {
                            return $er->createQueryBuilder('t')
                                    ->orderBy('t.name', 'ASC');
                        } else {
                            return $er->createQueryBuilder('t')
                                    ->where('t.group = ?1')
                                    ->orWhere('t.group is null')
                                    ->orderBy('t.name', 'ASC')
                                    ->setParameter(1, $group->getId());
                        }
                    },
                    'choice_label' => function ($template) {
                        return $template->getName();
                    },
                    'preferred_choices' => [$this->em->getRepository(Setting::class)->getValue('nullTemplate')],
                    'group_by' => function($choice, $key, $value) {
                        $out="Plantillas ";
                        if($choice->getGroup()!=null){
                            $out.="de ".$choice->getGroup()->getName();
                        }else{
                            $out.="globales";
                        }
                        return $out;
                    },
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
        ]);
    }

}
