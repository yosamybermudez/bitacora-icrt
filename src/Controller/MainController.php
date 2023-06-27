<?php

namespace App\Controller;

use App\Entity\GuardiaEquipo;
use App\Entity\Incidencia;
use App\Entity\Tarea;
use App\Entity\TareaEspecifica;
use App\Entity\TareaRecurrencia;
use App\Repository\GuardiaEquipoRepository;
use App\Repository\IncidenciaRepository;
use App\Repository\TareaEspecificaRepository;
use App\Repository\TareaRecurrenciaRepository;
use App\Service\BotTelegram;
use Doctrine\ORM\EntityManagerInterface;
use RRule\RRule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AppController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(IncidenciaRepository $incidenciaRepository,
                          TareaEspecificaRepository $tareaEspecificaRepository,
                          TareaRecurrenciaRepository $tareaRecurrenciaRepository,
                          GuardiaEquipoRepository $guardiaEquipoRepository,
                          BotTelegram $botTelegram): Response
    {


        $tareas_periodicas = $tareaRecurrenciaRepository->findTareasHoy($this->isGranted('ROLE_JEFE_INFORMATICA'));
        $tareas_especificas = $tareaEspecificaRepository->findTareasPendientes($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA'));
        $equipo = $guardiaEquipoRepository->findEquipoGuardiaHoy();
        if($this->getUser()){
            $incidencias_no_solucionadas = $incidenciaRepository->findIncidenciasNoSolucionadasPorArea($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA'));
        } else {
            $incidencias_no_solucionadas = $incidenciaRepository->findIncidenciasNoSolucionadas();
        }
        return $this->render('bitacora_principal/index.html.twig', [
            'guardia_hoy' => $equipo,
            'tareas_periodicas_hoy' => $tareas_periodicas,
            'tareas_especificas' => $tareas_especificas,
            'incidencias_no_solucionadas' => $incidencias_no_solucionadas
        ]);
    }

    /**
     * @Route("/perfil-usuario", name="app_user_profile")
     */
    public function profile(EntityManagerInterface $manager): Response
    {
        return $this->render('usuario/profile.html.twig', [
            'usuario' => $this->getUser()
        ]);
    }

    /**
     * @Route("/acerca-de", name="app_about")
     */
    public function about(): Response
    {
        return $this->render('bitacora_principal/acerca_de.html.twig', [
        ]);
    }
}
