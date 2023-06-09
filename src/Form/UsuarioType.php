<?php

namespace App\Form;

use App\Entity\Rol;
use App\Entity\Trabajador;
use App\Entity\Usuario;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\RolTransformer;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioType extends AbstractType
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                $builder->create('datos_generales', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true
                ])
                    ->add('username', TextType::class, array(
                        'label' => 'Usuario',
                        'row_attr' => array(
//                    'class' => 'col-sm-6'
                        ),
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                    ->add('trabajador', EntityType::class, [
                        'class' => Trabajador::class,
                        'label' => 'Trabajador asociado',
                        'placeholder' => 'Seleccione',
                        'choice_label' => 'nombreCompleto',
                        'mapped' => true,
                        'required' => true,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('t')
                                ->orderBy('t.nombres', 'ASC');
                        },
                    ])
                    ->add('roles', ChoiceType::class, array(
                        'choices' => $this->databaseRoles(),
                        'label' => 'Roles',
                        'attr' => [
                            'size' => 10
                        ],
                        'multiple' => true,
                        'expanded' => true,
                        'label_attr' => [
                            'class' => 'checkbox-switch checkbox-inline'
                        ],
                    ))
            )
            ->add(
                $builder->create('cambiar_contrasena', FormType::class, [
                    'mapped' => false,
                    'inherit_data' => true,
                ])
                    ->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Las contraseñas no coinciden.',
                        'options' => ['attr' => ['class' => 'password-field']],
                        'required' => false,
                        'first_options'  => ['label' => 'Contraseña'],
                        'second_options' => ['label' => 'Confirme la contraseña'],
                        'mapped' => $options['mapped']
                    ])
            );
    }

    private function databaseRoles() {

        $results = $this->manager->getRepository(Rol::class)->findAll();
        $roles = array();
        foreach($results as $r){
            $roles[$r->getNombre()] = $r->getIdentificador();
        }
        return $roles;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'required_password' => false,
            'mapped' => true
        ]);
    }
}
