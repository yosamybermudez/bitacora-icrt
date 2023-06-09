<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Credencial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CredencialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('destino', TextType::class, [
                'label' => 'Destino'
            ])
            ->add('descripcion', TextType::class, [
                'label' => 'Descripción'
            ])
            ->add('ips', TextType::class, [
                'label' => 'Dirección IP',
                'attr' => [
                    'class' => 'taginput taginput-ips'
                ],
                'label_attr' => [
                    'class' => 'label-tagsinput label-ips'
                ],
                'mapped' => true,
                'required' => false
            ])
            ->add('usuario')
            ->add('password', PasswordType::class, [
                'label' => 'Contraseña',
                'required' => $options['required_password']
            ])
            ->add('areas', EntityType::class, [
                'class' => Area::class,
                'label' => 'Área',
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione',
                'multiple' => true,
                'expanded' => true,
                'label_attr' => [
                    'class' => 'checkbox-switch checkbox-inline'
                ],
            ])
            ->add('protocolos', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'Áreas',
                'label_attr' => [
                    'class' => 'checkbox-switch checkbox-inline'
                ],
                'choices' => [
                    'SMB' => 'SMB',
                    'HTTP' => 'HTTP',
                    'FTP' => 'FTP',
                ]
            ])
        ;

        $builder->get('ips')
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
            'data_class' => Credencial::class,
            'required_password' => true
        ]);
    }
}
