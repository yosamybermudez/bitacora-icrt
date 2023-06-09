<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Credencial;
use App\Entity\Incidencia;
use App\Entity\IncidenciaTipo;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class IncidenciaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('problema_form', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true
                ])
                    ->add('titulo', TextType::class, [
                        'label' => 'Título',
                        'required' => true
                    ])
                    ->add('estado', ChoiceType::class, [
                        'label' => 'Estado',
                        'choices' => [
                            'Pendiente'   => 'pendiente',
                            'Solucionada' => 'solucionada'
                        ],
                    ])
                    ->add('etiquetas', TextType::class, [
                        'label' => 'Etiquetas',
                        'attr' => [
                            'class' => 'taginput taginput-etiquetas'
                        ],
                        'label_attr' => [
                            'class' => 'label-tagsinput label-etiquetas'
                        ],
                        'required' => false
                    ])
                    ->add('problema', CKEditorType::class, [
                        'required' => true,
                        'label' => 'Problema',
                        'attr' => [
                            'rows' => 5,
                        ],
                        'config' => [
                            'toolbar' => 'standard' //standard or full
                        ]
                    ])
                    ->add('tipo', EntityType::class, [
                        'label' => 'Tipo',
                        'class' => IncidenciaTipo::class,
                        'choice_label' => 'nombre',
                        'required' => true,
                        'attr' => [
                            'class' => 'd-flex'
                        ],
                        'choice_attr' => [
                            'class' => 'pe-2'
                        ],
                        'placeholder' => 'Seleccione'
                    ])
                    ->add('credencial', EntityType::class, [
                        'label' => 'Credencial',
                        'class' => Credencial::class,
                        'choice_label' => 'destino',
                        'placeholder' => 'Seleccione',
                        'required' => false,
                    ])
                    ->add('areas', EntityType::class, [
                        'label' => 'Áreas',
                        'class' => Area::class,
                        'choice_label' => 'nombre',
                        'multiple' => true,
                        'expanded' => true,
                        'required' => true,
                        'attr' => [
                            'class' => 'd-flex'
                        ],
                        'choice_attr' => [
                            'class' => 'pe-2'
                        ],
                        'label_attr' => [
                            'class' => 'checkbox-inline checkbox-switch me-3'
                        ],
                        'placeholder' => 'Seleccione'
                    ])
                    ->add('adjuntoProblemaFile', VichFileType::class, [
                        'required' => false,
                        'allow_delete' => true,
                        'delete_label' => 'Eliminar adjunto',
                        'download_label' => 'Descargar adjunto',
                        'asset_helper' => true,
                        'label' => 'Adjunto (problema)',
                    ])
            )
            ->add(
                $builder->create('solucion_form', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true
                ])
                    ->add('solucion', CKEditorType::class, [
                        'label' => 'Solución',
                        'attr' => [
                            'rows' => 5
                        ],
                        'required' => true,
                        'config' => [
                            'toolbar' => 'standard' //standard or full
                        ]
                    ])
                    ->add('adjuntoSolucionFile', VichFileType::class, [
                        'required' => false,
                        'allow_delete' => true,
                        'delete_label' => 'Eliminar adjunto',
                        'download_label' => 'Descargar adjunto',
                        'asset_helper' => true,
                        'label' => 'Adjunto (Solución)'
                    ])
            )
        ;
        $builder->get('problema_form')->get('etiquetas')
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
            'data_class' => Incidencia::class,
        ]);
    }
}
