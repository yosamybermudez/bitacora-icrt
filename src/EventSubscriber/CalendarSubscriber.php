<?php


namespace App\EventSubscriber;

use App\Entity\GuardiaEquipo;
use App\Repository\EventoCalendarioRepository;
use App\Repository\GuardiaEquipoRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Faker\Factory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $guardiaEquipoRepository;
    private $router;

    public function __construct(
        GuardiaEquipoRepository $guardiaEquipoRepository,
        UrlGeneratorInterface $router
    )
    {
        $this->guardiaEquipoRepository = $guardiaEquipoRepository;
        $this->router = $router;

    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        $guardia_equipos = $this->guardiaEquipoRepository
            ->findAll();

        foreach ($guardia_equipos as $equipo){
            $texto =
            $equipo->getInformaticoVsn1()->getNombreCorto(). " "  . ' - ' .
            $equipo->getInformaticoVsn2()->getNombreCorto() . ' - ' .
            $equipo->getInformaticoCorporativa()->getNombreCorto() . ' - ' .
            $equipo->getTecnicoEstudio()->getNombreCorto() . ' - ';
            $faker = Factory::create();

            $color = $faker->hexColor;
            foreach ($equipo->getFechasPlanificadas() as $fecha){
                $calendarEvent = new Event(
                    $texto,
                    \DateTime::createFromFormat('d-m-Y', $fecha)->setTime(4,0,0),
                    null
                );
                $calendarEvent->setOptions([
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                ]);
                $calendarEvent->addOption(
                    'url',
                    $this->router->generate('app_guardia_equipo_show', [
                        'id' => $equipo->getId(),
                    ])
                );

                // finally, add the event to the CalendarEvent to fill the calendar
                $calendar->addEvent($calendarEvent);
            }
        }
    }
}