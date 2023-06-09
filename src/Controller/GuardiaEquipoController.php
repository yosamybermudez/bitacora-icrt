<?php

namespace App\Controller;

use App\Entity\GuardiaEquipo;
use App\Form\GuardiaEquipoType;
use App\Repository\FechaConmemorativaRepository;
use App\Repository\GuardiaEquipoRepository;
use App\Repository\TrabajadorRepository;
use Faker\Factory;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use RRule\RRule;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/guardia-equipo")
 */
class GuardiaEquipoController extends AppController
{
    /**
     * @Route("/", name="app_guardia_equipo_index", methods={"GET"})
     */
    public function index(GuardiaEquipoRepository $guardiaEquipoRepository): Response
    {
        return $this->render('guardia_equipo/index.html.twig', [
            'guardia_equipos' => $guardiaEquipoRepository->findBy(array(), array('nombre' => 'ASC')),
        ]);
    }

    /**
     * @Route("/new", name="app_guardia_equipo_new", methods={"GET", "POST"})
     */
    public function new(Request $request, GuardiaEquipoRepository $guardiaEquipoRepository): Response
    {
        $guardiaEquipos = $guardiaEquipoRepository->findAll();
        if (count($guardiaEquipos) === 4) {
            $this->addCustomError('No puede agregar más equipos de guardia');
            return $this->redirectToRoute('app_guardia_equipo_index', []);
        }
        $guardiaEquipo = new GuardiaEquipo();
        $form = $this->createForm(GuardiaEquipoType::class, $guardiaEquipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $guardiaEquipo = $this->handleGuardiaRecurrencia($guardiaEquipo);
            $guardiaEquipoRepository->add($guardiaEquipo, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_guardia_equipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('guardia_equipo/new.html.twig', [
            'guardia_equipo' => $guardiaEquipo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_guardia_equipo_show", methods={"GET"})
     */
    public function show(GuardiaEquipo $guardiaEquipo): Response
    {
        $rrule = new RRule($guardiaEquipo->getRecurrencia());

        return $this->render('guardia_equipo/show.html.twig', [
            'guardia_equipo' => $guardiaEquipo,
            'proximas_guardias' => $rrule->getOccurrencesAfter(new \DateTime(), true,14)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_guardia_equipo_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, GuardiaEquipo $guardiaEquipo, GuardiaEquipoRepository $guardiaEquipoRepository): Response
    {
        $form = $this->createForm(GuardiaEquipoType::class, $guardiaEquipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $guardiaEquipo = $this->handleGuardiaRecurrencia($guardiaEquipo);
            $guardiaEquipoRepository->add($guardiaEquipo, $this->getUser(), true);
            $this->flasher->addUpdated();
            return $this->redirectToRoute('app_guardia_equipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('guardia_equipo/new.html.twig', [
            'guardia_equipo' => $guardiaEquipo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_guardia_equipo_delete", methods={"POST"})
     */
    public function delete(Request $request, GuardiaEquipo $guardiaEquipo, GuardiaEquipoRepository $guardiaEquipoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$guardiaEquipo->getId(), $request->request->get('_token'))) {
            $guardiaEquipoRepository->remove($guardiaEquipo, true);
        }
        $this->addDeleted();
        return $this->redirectToRoute('app_guardia_equipo_index', [], Response::HTTP_SEE_OTHER);
    }

    public function handleGuardiaRecurrencia(GuardiaEquipo $guardiaEquipo): GuardiaEquipo
    {
        $rrule_array = [];
        $rrule_array['DTSTART'] = $guardiaEquipo->getFechaReferencia();
        $rrule_array['FREQ'] = 'DAILY';
        $rrule_array['INTERVAL'] = 4;
        $rrule = new RRule(
            $rrule_array
        );

        $guardiaEquipo->setRecurrencia($rrule->rfcString());

        return $guardiaEquipo;
    }
}
