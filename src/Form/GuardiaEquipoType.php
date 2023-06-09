<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\GuardiaEquipo;
use App\Entity\Trabajador;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuardiaEquipoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre'
            ])
            ->add('informatico_vsn_1', EntityType::class, [
                'class' => Trabajador::class,
                'choice_label' => 'nombre_completo',
                'placeholder' => 'Seleccione',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('t')
                        ->innerjoin(Area::class, 'a', Join::WITH, 't.area = a.id')
                        ->where('a.nombre=\'VSN\'')
                        ->orderBy('t.nombres', 'ASC');
                },
            ])
            ->add('informatico_vsn_2', EntityType::class, [
                'class' => Trabajador::class,
                'choice_label' => 'nombre_completo',
                'placeholder' => 'Seleccione',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('t')
                        ->innerjoin(Area::class, 'a', Join::WITH, 't.area = a.id')
                        ->where('a.nombre=\'VSN\'')
                        ->orderBy('t.nombres', 'ASC');
                },
            ])
            ->add('informatico_corporativa', EntityType::class, [
                'class' => Trabajador::class,
                'choice_label' => 'nombre_completo',
                'placeholder' => 'Seleccione',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('t')
                        ->innerjoin(Area::class, 'a', Join::WITH, 't.area = a.id')
                        ->where('a.nombre=\'Corporativa\'')
                        ->orderBy('t.nombres', 'ASC');
                },
            ])
            ->add('tecnico_estudio', EntityType::class, [
                'class' => Trabajador::class,
                'choice_label' => 'nombre_completo',
                'placeholder' => 'Seleccione',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('t')
                        ->innerjoin(Area::class, 'a', Join::WITH, 't.area = a.id')
                        ->where('a.nombre=\'Estudio\'')
                        ->orderBy('t.nombres', 'ASC');
                },
            ])
            ->add('fecha_referencia', DateType::class, [
                'label' => 'Fecha de referencia 24 x 72',
                'widget' => 'single_text'
            ])
            ->add('colorCalendario', ColorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GuardiaEquipo::class,
        ]);
    }
}
