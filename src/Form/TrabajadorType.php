<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Cargo;
use App\Entity\Trabajador;
use App\Entity\Usuario;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Cropperjs\Form\CropperType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrabajadorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fotoFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Eliminar foto',
                'download_label' => 'Descargar foto',
                'asset_helper' => true,
                'label' => 'Foto',
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'image/jpg', 'image/jpeg', 'image/png'
                        ],
                        'mimeTypesMessage' => 'Formatos permitidos (.jpg, .jpeg, .png)'
                    ])
                ]
            ])
            ->add('nombres', TextType::class, [
                'label' => 'Nombre(s)',
                'attr' => [
//                    'placeholder' => 'Nombre(s)'
                ],
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('apellidos', TextType::class, [
                'label' => 'Apellidos',
                'attr' => [
//                    'placeholder' => 'Apellidos'
                ],
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('carne_identidad', TextType::class, [
                'label' => 'Carné de identidad',
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('sexo', ChoiceType::class, [
                'label' => 'Sexo',
                'choices' => ['Masculino' => 'M', 'Femenino' => 'F']
            ])
            ->add('telefono_principal', TextType::class, [
                'label' => 'Teléfono principal',
                'attr' => [
//                    'placeholder' => 'Teléfono principal'
                ],
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('telefono_alternativo', TextType::class, [
                'label' => 'Teléfono alternativo',
                'required' => false,
                'attr' => [
//                    'placeholder' => 'Teléfono alternativo'
                ],
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('correo_electronico', TextType::class, [
                'label' => 'Correo electrónico',
                'required' => false,
                'attr' => [
                    'placeholder' => 'nombre.apellido@icrt.cu'
                ],
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('direccion', TextType::class, [
                'label' => 'Dirección',
                'attr' => [
//                    'placeholder' => 'Dirección'
                ],
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('area', EntityType::class, [
                'class' => Area::class,
                'choice_label' => 'nombre',
                'label' => 'Área',
                'placeholder' => 'Seleccione',
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('cargo', EntityType::class, [
                'class' => Cargo::class,
                'choice_label' => 'nombre',
                'label' => 'Cargo',
                'required' => false,
                'placeholder' => 'Seleccione',
                'attr' => [
//                    'placeholder' => 'Dirección'
                ],
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('fechaAlta', DateType::class, [
                'label' => 'Fecha de alta',
                'widget' => 'single_text'
            ])
			->add('telegram_id', TextType::class, [
                'label' => 'Telegram ID',
				'required' => false,
                'row_attr' => [
                    'class' => 'mb-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trabajador::class,
        ]);
    }
}
