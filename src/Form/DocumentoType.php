<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Documento;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocumentoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título'
            ])
            ->add('descripcion', CKEditorType::class, [
                'label' => 'Descripción'
            ])
            ->add('adjuntoFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Eliminar adjunto',
                'download_label' => 'Descargar adjunto',
                'asset_helper' => true,
                'label' => 'Documento',
                'constraints' => [
                    new File([
                        'maxSize' => '4M',
                        'mimeTypes' => [
                            'application/pdf', 'application/x-pdf',
                            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                        ],
                        'mimeTypesMessage' => 'Formatos permitidos (.pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx)'
                    ])
                ]
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Documento::class,
        ]);
    }
}
