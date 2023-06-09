<?php

namespace App\DataFixtures;

use App\Entity\Area;
use App\Entity\Cargo;
use App\Entity\IncidenciaTipo;
use App\Entity\JobContract;
use App\Entity\Person;
use App\Entity\Rol;
use App\Entity\TareaTipo;
use App\Entity\Trabajador;
use App\Entity\User;
use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        //Usuario Admin
        $usuario = new Usuario();
        $usuario->setUsername('admin');
        $usuario->setRoles(array('ROLE_ADMINISTRADOR_SISTEMA'));
        $hashedPassword = $this->passwordHasher->hashPassword(
            $usuario,
            'admin'
        );
        $usuario->setPassword($hashedPassword);
        $manager->getRepository(Usuario::class)->add($usuario, null, true);
        //Roles
        $roles_array = array(
            'ADMINISTRADOR_SISTEMA' => 'Administrador del sistema',
            'DIRECTOR_TECNICO' => 'Director técnico',
            'PERMISOS_ESPECIALES' => 'Usuario con permisos especiales',
            'JEFE_INFORMATICA' => 'Jefe de informática',
            'TECNICO_ESTUDIO' => 'Técnico de estudio',
            'INFORMATICO_CORPORATIVA' => 'Informático de Red Corporativa',
            'INFORMATICO_VSN' => 'Informático de Red VSN',
        );
        foreach ($roles_array as $identificador => $nombre){
            $rol = new Rol();
            $rol->setNombre($nombre);
            $rol->setIdentificador("ROLE_" . $identificador);
            $manager->getRepository(Rol::class)->add($rol, $usuario, true);
        }

        //Cargo
        $cargo = new Cargo();
        $cargo->setNombre('Sin cargo');
        $cargo->setEscalaSalarial('I');
        $cargo->setSalarioEscala(0);
        $manager->getRepository(Cargo::class)->add($cargo, $usuario, true);

        $cargo = new Cargo();
        $cargo->setNombre('Administrador del Sistema');
        $cargo->setEscalaSalarial('I');
        $cargo->setSalarioEscala(0);
        $manager->getRepository(Cargo::class)->add($cargo, $usuario, true);

        //Area
        $area = new Area();
        $area->setNombre('Sin área');
        $area->setDescripcion('Sin descripción');
        $manager->getRepository(Area::class)->add($area, $usuario, true);

        //Tipo de tarea
        $tipoTarea = new TareaTipo();
        $tipoTarea->setNombre('Otro');
        $tipoTarea->setDescripcion('Sin descripción');
        $manager->getRepository(TareaTipo::class)->add($tipoTarea, $usuario, true);

        //Tipo de incidencia
        $tipoIncidencia = new IncidenciaTipo();
        $tipoIncidencia->setNombre('Otro');
        $tipoIncidencia->setDescripcion('Sin descripción');
        $manager->getRepository(IncidenciaTipo::class)->add($tipoTarea, $usuario, true);

        $tipoIncidencia = new IncidenciaTipo();
        $tipoIncidencia->setNombre('Cambio de contraseña');
        $tipoIncidencia->setDescripcion('Sin descripción');
        $manager->getRepository(IncidenciaTipo::class)->add($tipoTarea, $usuario, true);

        //Trabajador Administrador del Sistema
        $trabajador = new Trabajador();
        $trabajador->setNombres('Administrador');
        $trabajador->setApellidos('del Sistema');
        $trabajador->setCargo($cargo);
        $trabajador->setArea($area);
        $trabajador->setFake(true);
        $trabajador->setUsuario($usuario);
        $manager->getRepository(Trabajador::class)->add($trabajador, $usuario, true);

    }
}
