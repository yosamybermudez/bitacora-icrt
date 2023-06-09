<?php

namespace App\Form;

use App\Entity\CronTareaProgramada;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronTareaProgramadaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('habilitada', CheckboxType::class, ['required' => false])
            ->add('usa_token', CheckboxType::class, ['required' => false])
            ->add('username')
            ->add('password')
            ->add('descripcion', TextType::class, [
                'required' => false
            ])
            ->add(
                $builder->create('repeticion', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true,
                    'required' => false
                ])
                    ->add('frequency', ChoiceType::class, [
                        'label'=> 'Frecuencia',
                        'choices' => [
                            'Diario' => 'DAILY',
                            'Semanal' => 'WEEKLY',
                            'Mensual' => 'MONTHLY',
                            'Anual' => 'YEARLY',
                        ],
                        'mapped' => false,
                        'placeholder' => 'Seleccione'
                    ])
                    ->add('interval', NumberType::class, [
                        'label' => 'Intervalo',
                        'mapped' => false,
                        'data' => 1
                    ])
                    ->add('hora', TimeType::class, [
                        'widget' => 'single_text',
                        'required' => false,
                        'mapped' => false
                    ])
                    ->add('by_day', ChoiceType::class, [
                        'label' => 'Días',
                        'choices' => [
                            'L' => 'MON',
                            'M' => 'TUE',
                            'Mi' => 'WED',
                            'J' => 'THU',
                            'V' => 'FRR',
                            'S' => 'SAT',
                            'D' => 'SUN',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CronTareaProgramada::class,
        ]);
    }
}
