<?php

namespace App\Controller;

use App\Entity\Tarea;
use App\Entity\TareaEspecifica;
use App\Entity\TareaRecurrencia;
use App\Entity\Usuario;
use App\Form\TareaEspecificaType;
use App\Form\TareaType;
use App\Repository\TareaEspecificaRepository;
use App\Repository\TareaRecurrenciaRepository;
use App\Repository\TareaRepository;
use Doctrine\ORM\EntityManagerInterface;
use RRule\RRule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tarea")
 */
class TareaController extends AppController
{

    /**
     * @Route("/", name="app_tarea_index", methods={"GET"})
     */
    public function index(TareaRecurrenciaRepository $tareaRecurrenciaRepository, TareaEspecificaRepository $tareaEspecificaRepository): Response
    {
        $tareas_periodicas = $tareaRecurrenciaRepository->findTareasArea($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA'));
        $tareas_especificas = $tareaEspecificaRepository->findTareasPendientes($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA'));
        return $this->render('tarea/index.html.twig', [
            'tareas_especificas' => $tareas_especificas,
            'tareas_periodicas' => $tareas_periodicas
        ]);
    }

    /**
     * @Route("/periodicas", name="app_tareas_periodicas", methods={"GET"})
     */
    public function periodicas(TareaRecurrenciaRepository $tareaRecurrenciaRepository): Response
    {
        return $this->render('tarea/index.html.twig', [
            'tareas_periodicas' => $tareaRecurrenciaRepository->findTareasArea($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA')),
            'categoria' => 'periódicas'
        ]);
    }

    /**
     * @Route("/especificas", name="app_tareas_especificas", methods={"GET"})
     */
    public function especificas(TareaEspecificaRepository $tareaEspecificaRepository): Response
    {
        $tareas_especificas = $tareaEspecificaRepository->findTareasPendientes($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA'));

        return $this->render('tarea/index.html.twig', [
            'tareas_especificas' => $tareas_especificas,
            'categoria' => 'específicas'
        ]);
    }


    /**
     * @Route("/new", name="app_tarea_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, TareaRepository $tareaRepository, TareaRecurrenciaRepository $tareaRecurrenciaRepository, TareaEspecificaRepository $tareaEspecificaRepository): Response
    {
        $tarea = new Tarea();
        $form = $this->createForm(TareaType::class, $tarea);
        $form->get('repeticion')->get('interval')->setData(1);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tareaRepository->add($tarea, $this->getUser());

            if($tarea->isPeriodica()){
                $tarea_recurrencia = $this->handleTareaRecurrencia($request, $tarea);
                $tareaRecurrenciaRepository->add($tarea_recurrencia, $this->getUser(), true);
                $this->addCreated();
                return $this->redirectToRoute('app_tareas_periodicas', [], Response::HTTP_SEE_OTHER);
            } else {
                $tarea_especifica = $this->handleTareaEspecifica($request, $tarea, $entityManager);
                $tareaEspecificaRepository->add($tarea_especifica, $this->getUser(), true);
                $this->addCreated();
                return $this->redirectToRoute('app_tareas_especificas', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('tarea/new.html.twig', [
            'tarea' => $tarea,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarea_show", methods={"GET"})
     */
    public function show(Tarea $tarea): Response
    {
        return $this->render('tarea/show.html.twig', [
            'tarea' => $tarea,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_tarea_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Tarea $tarea, TareaRepository $tareaRepository, TareaRecurrenciaRepository $tareaRecurrenciaRepository): Response
    {
        $form = $this->createForm(TareaType::class, $tarea);
        if($tarea->isPeriodica() and $tarea->getTareaRecurrencia()){
            $rrule = new RRule(
                $tarea->getTareaRecurrencia()->getRecurrencia()
            );
            $form->get('repeticion')->get('by_month_days')->setData(explode(',', $rrule->getRule()['BYMONTHDAY']));
        }
        if($tarea->isPeriodica() && $tarea->getTareaRecurrencia()){
            $rrule = new RRule($tarea->getTareaRecurrencia()->getRecurrencia());
            $form->get('repeticion')->get('fecha_inicio')->setData($rrule->getRule()['DTSTART']);
            $form->get('repeticion')->get('frequency')->setData($rrule->getRule()['FREQ']);
            $form->get('repeticion')->get('interval')->setData($rrule->getRule()['INTERVAL']);
            $form->get('repeticion')->get('by_day')->setData(explode(',', $rrule->getRule()['BYDAY']));
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $tareaRepository->add($tarea, $this->getUser());
            if($tarea->isPeriodica()){
                $tarea_recurrencia = $this->handleTareaRecurrencia($request, $tarea);
                $tareaRecurrenciaRepository->add($tarea_recurrencia, $this->getUser(), true);
                $this->flasher->addUpdated();
                return $this->redirectToRoute('app_tareas_periodicas', [], Response::HTTP_SEE_OTHER);
            } else {
                $tarea_recurrencia = $this->handleTareaRecurrencia($request, $tarea);
                $tareaRecurrenciaRepository->add($tarea_recurrencia,  $this->getUser(), true);
                $this->flasher->addUpdated();
                return $this->redirectToRoute('app_tareas_especificas', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('tarea/new.html.twig', [
            'tarea' => $tarea,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/registar_solucion", name="app_tarea_registrar_solucion", methods={"GET", "POST"})
     */
    public function registarSolucion(Request $request, TareaEspecifica $tareaEspecifica, TareaEspecificaRepository $tareaEspecificaRepository): Response
    {
        $form = $this->createForm(TareaEspecificaType::class, $tareaEspecifica);
               $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            $incidencium->setEstado('solucionada');
//            $incidenciaRepository->add($incidencium, $this->getUser(), true);
            $this->addUpdated();
            return $this->redirectToRoute('app_incidencia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarea/solucion.html.twig', [
            'tarea' => $tareaEspecifica,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarea_delete", methods={"POST"})
     */
    public function delete(Request $request, Tarea $tarea, TareaRepository $tareaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tarea->getId(), $request->request->get('_token'))) {
            $tareaRepository->remove($tarea, true);
        }
        $this->addDeleted();
        return $this->redirectToRoute('app_tarea_index', [], Response::HTTP_SEE_OTHER);
    }


    public function handleTareaRecurrencia(Request $request, Tarea $tarea): TareaRecurrencia
    {
        $evento_recurrencia = $request->request->get('tarea')['repeticion'];
        $month_days = $request->request->get('month_days_hidden');
        $rrule_array = [];
        if(isset($evento_recurrencia['fecha_inicio']) && $evento_recurrencia['fecha_inicio'] != '') {
            //$rule->setStartDate($evento_recurrencia['fecha_inicio']);
            $rrule_array['DTSTART'] = $evento_recurrencia['fecha_inicio'];
        }

        if(isset($evento_recurrencia['frequency']) && $evento_recurrencia['frequency'] != ''){
            //$rule->setFreq($evento_recurrencia['frequency']);
            $rrule_array['FREQ'] = $evento_recurrencia['frequency'];
        }

        if(isset($evento_recurrencia['interval']) && $evento_recurrencia['interval'] > 0){
//            $rule->setInterval($evento_recurrencia['interval']);
            $rrule_array['INTERVAL'] = $evento_recurrencia['interval'];
        }

        if(isset($evento_recurrencia['by_day']) && count($evento_recurrencia['by_day']) > 0){
//            $rule->setByDay($evento_recurrencia['by_day']);
            $rrule_array['BYDAY'] = $evento_recurrencia['by_day'];
        }

        if($month_days){
            $rrule_array['BYMONTHDAY'] = $month_days;
        }
        $rrule = new RRule(
            $rrule_array
        );

        $tarea_recurrencia = $tarea->getTareaRecurrencia() ?: new TareaRecurrencia();
        $tarea_recurrencia->setTarea($tarea);
        if(!$tarea->getTareaRecurrencia() or $tarea->getTareaRecurrencia()->getRecurrencia() !== $rrule->rfcString()){
            $tarea_recurrencia->setFechaInicio(new \DateTime($rrule_array['DTSTART']));
            $tarea_recurrencia->setRecurrencia($rrule->rfcString());
        }
        return $tarea_recurrencia;
    }

    public function handleTareaEspecifica(Request $request, Tarea $tarea, EntityManagerInterface $entityManager): TareaEspecifica
    {
        $evento_tarea_especifica = $request->request->get('tarea')['tarea_especifica'];
        $tarea_especifica = $tarea->getTareaEspecifica() ?: new TareaEspecifica();
        if(isset($evento_tarea_especifica['asignado_a']) && count($evento_tarea_especifica['asignado_a']) > 0){
            foreach ($evento_tarea_especifica['asignado_a'] as $usuario){
                $usuario = $entityManager->getRepository(Usuario::class)->find($usuario);
                $tarea_especifica->addAsignadoA($usuario);
            }
        }
        if(isset($evento_tarea_especifica['fecha_tope_cumplimiento']) && $evento_tarea_especifica['fecha_tope_cumplimiento'] !== ''){
           $tarea_especifica->setFechaCumplimiento(new \DateTime($evento_tarea_especifica['fecha_tope_cumplimiento']));
        }
        $tarea_especifica->setTarea($tarea);
        return $tarea_especifica;
    }
}
