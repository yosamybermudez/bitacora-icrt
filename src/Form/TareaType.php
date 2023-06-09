<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Tarea;
use App\Entity\TareaTipo;
use App\Entity\Trabajador;
use App\Entity\Usuario;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TareaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('general', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true
                ])
                    ->add('titulo', TextType::class, [
                        'label' => 'Título'
                    ])
                    ->add('descripcion', CKEditorType::class, [
                        'label' => 'Descripción',
                        'attr' => [
                            'rows' => 5
                        ],
                        'data' => 'Sin descripción',
                        'required' => true,
                        'config' => [
                            'toolbar' => 'standard' //standard or full
                        ]
                    ])
                    ->add('areas', EntityType::class, [
                        'class' => Area::class,
                        'choice_label' => 'nombre',
                        'placeholder' => 'Seleccione',
                        'multiple' => true,
                        'expanded' => true,
                        'label' => 'Áreas',
                        'label_attr' => [
                            'class' => 'checkbox-switch checkbox-inline'
                        ],
                    ])
                    ->add('tipo', EntityType::class, [
                        'class' => TareaTipo::class,
                        'choice_label' => 'nombre',
                        'placeholder' => 'Seleccione',
                        'label' => 'Tipo'
                    ])
                    ->add('periodica', CheckboxType::class, [
                        'label' => 'Tarea periódica',
                        'label_attr' => [
                            'class' => 'checkbox-switch'
                        ],
                        'required' => false
                    ])
            )
            ->add(
                $builder->create('repeticion', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true,
                    'required' => false
                ])
                    ->add('fecha_inicio', DateType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'mapped' => false
                    ])
                    ->add('frequency', ChoiceType::class, [
                        'label'=> 'Frecuencia',
                        'choices' => [
                            'No se repite' => 'NO',
                            'Diario' => 'DAILY',
                            'Semanal' => 'WEEKLY',
                            'Mensual' => 'MONTHLY',
                            'Anual' => 'YEARLY',
                        ],
                        'mapped' => false
                    ])
                    ->add('interval', NumberType::class, [
                        'label' => 'Intervalo',
                        'mapped' => false
                    ])
                    ->add('by_day', ChoiceType::class, [
                        'label' => 'Días',
                        'choices' => [
                            'L' => 'MO',
                            'M' => 'TU',
                            'Mi' => 'WE',
                            'J' => 'TH',
                            'V' => 'FR',
                            'S' => 'SA',
                            'D' => 'SU',
                        ],
                        'multiple' => true,
                        'expanded' => true,
                        'mapped' => false,
                        'label_attr' => [
                            'class' => 'checkbox-inline checkbox-switch'
                        ],
                    ])
                    ->add('by_month_days', TextType::class, [
                        'label' => 'Días del mes',
                        'mapped' => false,
                        'attr' => [
                            'class' => 'taginput taginput-monthdays'
                        ],
                        'label_attr' => [
                            'class' => 'label-tagsinput label-monthdays'
                        ],
                    ])
            )
            ->add(
                $builder->create('tarea_especifica', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true,
                    'required' => false
                ])
                    ->add('asignado_a', EntityType::class, [
                        'class' => Usuario::class,
                        'choice_label' => 'username',
                        'placeholder' => 'Seleccione',
                        'label' => 'Asignada a',
                        'multiple' => true,
                        'mapped' => false
                    ])
                    ->add('fecha_tope_cumplimiento', DateType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'empty_data' => '',
                        'mapped' => false
                    ])
            )
        ;

        $builder->get('repeticion')->get('by_month_days')
            ->addModelTransformer(new CallbackTransformer(
                function ($ipsAsArray) {
                    return $ipsAsArray ? implode(',', $ipsAsArray) : '';
                },
                function ($ipsAsString) {
                    return $ipsAsString ? explode(',', $ipsAsString) : [];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tarea::class,
        ]);
    }
}
