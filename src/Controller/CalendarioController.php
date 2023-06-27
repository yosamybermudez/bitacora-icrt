<?php

namespace App\Controller;

use App\Repository\FechaConmemorativaRepository;
use App\Repository\GuardiaEquipoRepository;
use App\Repository\TrabajadorRepository;
use RRule\RRule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/calendario")
 */
class CalendarioController extends AbstractController
{
    /**
     * @Route("/", name="app_calendario")
     */
    public function index(): Response
    {
        return $this->render('calendario/index.html.twig', []);
    }

    /**
     * @Route("/eventos", name="app_calendario_fetch_eventos")
     */
    public function fetchEventos(GuardiaEquipoRepository $guardiaEquipoRepository, FechaConmemorativaRepository $fechaConmemorativaRepository, TrabajadorRepository $trabajadorRepository){
        $calendar = array();

        $calendar =array_merge($calendar, $this->getCumpleannos($trabajadorRepository));
        $calendar = array_merge($calendar, $this->getFechasConmemorativas($fechaConmemorativaRepository));
        $calendar = array_merge($calendar, $this->getGuardiasEquipos($guardiaEquipoRepository));

        return new JsonResponse($calendar);
    }

    //03:00
    public function getCumpleannos(TrabajadorRepository $trabajadorRepository){
        $calendar = [];
        $cumpleanos = $trabajadorRepository->findAllCumpleannos();
        foreach ($cumpleanos as $cumpleano)
        {
            $rrule_string = sprintf('FREQ=YEARLY;BYMONTH=%s;BYMONTHDAY=%s', $cumpleano['fecha']->format('n'), $cumpleano['fecha']->format('j'));
            $rrule = new RRule($rrule_string, date('Y-m-d', strtotime("-6 months")));
            $fechas = $rrule->getOccurrencesBetween(date('Y-m-d', strtotime("-6 months")), date('Y-m-d', strtotime('+6 months')));

            foreach ($fechas as $fecha){
                $edad = $fecha->diff($cumpleano['fecha'])->y;
                $event = $fecha > new \DateTime() ? 'cumplirá' : 'cumplió';
                $fecha = $fecha->format('Y-m-d');
                $calendarEvent = array(
                    'title' => sprintf('%s %s %s años', $cumpleano['trabajador'], $event, $edad),
                    'start' => date('Y-m-d\\TH:i:s.u\\Z', strtotime($fecha . " 03:00:00")),
                    'end' => null,
                    'allDay' => true,
                    'color' => '#696cff29',
                    'textColor' => '#696cff',
                    'constraint' => 'birthdate'
                );

                $calendar[] = $calendarEvent;
            }
        }
        return $calendar;
    }

    //04:00
    public function getFechasConmemorativas(FechaConmemorativaRepository $fechaConmemorativaRepository){
        $calendar = [];
        $fechas_conmemorativas = $fechaConmemorativaRepository->findAll();
        foreach ($fechas_conmemorativas as $fecha){

            if($fecha->getFecha()){
                $calendarEvent = array(
                    'title' => $fecha->getConmemoracion(),
                    'start' => date('Y-m-d\\TH:i:s.u\\Z', strtotime($fecha->getFecha()->format('Y-m-d') . " 04:00:00")),
                    'end' => null,
                    'allDay' => true,
                    'color' => '#696cff',
//                    'url' => $this->generateUrl('app_guardia_equipo_show', [
//                        'id' => $equipo->getId(),
//                    ])
                );
                $calendar[] = $calendarEvent;
            } elseif($fecha->getRecurrencia()){
                $rrule = new RRule($fecha->getRecurrencia(),date('Y') . '-01-01');
                $recurrencias = $rrule->getOccurrencesBetween(date('Y') . '-01-01',date('Y') . '-12-31');

                foreach ($recurrencias as $recurrencia){
                    $calendarEvent = array(
                        'title' => $fecha->getConmemoracion(),
                        'start' => date('Y-m-d\\TH:i:s.u\\Z', strtotime($recurrencia->format('Y-m-d') . " 04:00:00")),
                        'end' => null,
                        'allDay' => true,
                        'color' => '#696cff',
//                    'url' => $this->generateUrl('app_guardia_equipo_show', [
//                        'id' => $equipo->getId(),
//                    ])
                    );
                    $calendar[] = $calendarEvent;
                }
            }

        }
        return $calendar;
    }

    //05:00
    public function getGuardiasEquipos(GuardiaEquipoRepository $guardiaEquipoRepository){
        $calendar = [];
        $guardia_equipos = $guardiaEquipoRepository->findAll();
        foreach ($guardia_equipos as $equipo){
            $texto = [];
            if($equipo->getInformaticoVsn1()){ $texto[] = 'VSN: ' . $equipo->getInformaticoVsn1()->getNombreCorto(); }
            if($equipo->getInformaticoVsn2()){ $texto[] = 'VSN: ' . $equipo->getInformaticoVsn2()->getNombreCorto(); }
            if($equipo->getInformaticoCorporativa()){ $texto[] = 'Corp: ' . $equipo->getInformaticoCorporativa()->getNombreCorto(); }
            if($equipo->getTecnico()){ $texto[] = 'Taller: ' . $equipo->getTecnico()->getNombreCorto(); }

            $rrule = new RRule($equipo->getRecurrencia());
//            $fechas = $rrule->getOccurrencesBetween(date('Y-m-d', strtotime('01/01')), date('Y-m-d', strtotime('12/31')));
            $fechas = $rrule->getOccurrencesBetween(date('Y-m-d', strtotime("-6 months")), date('Y-m-d', strtotime('+6 months')));
            foreach ($fechas as $fecha){
                $fecha = $fecha->format('Y-m-d');
                foreach ($texto as $t){
                    $calendarEvent = array(
                        'title' => $t,
                        'start' => date('Y-m-d\\TH:i:s.u\\Z', strtotime($fecha . " 05:00:00")),
                        'end' => null,
                        'allDay' => true,
                        'color' => $equipo->getColorCalendario(),
                        'url' => $this->generateUrl('app_guardia_equipo_show', [
                            'id' => $equipo->getId(),
                        ])
                    );

                    $calendar[] = $calendarEvent;
                }
            }
        }
        return $calendar;
    }

}
