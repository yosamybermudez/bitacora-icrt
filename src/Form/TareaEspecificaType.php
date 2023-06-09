<?php

namespace App\Form;

use App\Entity\TareaEspecifica;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TareaEspecificaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('observaciones', CKEditorType::class, [
                'label' => 'Solución',
                'attr' => [
                    'rows' => 5
                ],
                'required' => false,
                'config' => [
                    'toolbar' => 'basic' //standard or full
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TareaEspecifica::class,
        ]);
    }
}
