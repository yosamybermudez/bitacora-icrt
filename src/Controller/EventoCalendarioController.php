<?php

namespace App\Controller;

use App\Entity\EventoCalendario;
use App\Form\EventoCalendarioType;
use App\Repository\EventoCalendarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evento-calendario")
 */
class EventoCalendarioController extends AppController
{
    /**
     * @Route("/", name="app_evento_calendario_index", methods={"GET"})
     */
    public function index(EventoCalendarioRepository $eventoCalendarioRepository): Response
    {
        return $this->render('evento_calendario/index.html.twig', [
            'evento_calendarios' => $eventoCalendarioRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_evento_calendario_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EventoCalendarioRepository $eventoCalendarioRepository): Response
    {
        $eventoCalendario = new EventoCalendario();
        $form = $this->createForm(EventoCalendarioType::class, $eventoCalendario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventoCalendarioRepository->add($eventoCalendario, $this->getUser(), true);
            $this->addFlashAgregadoSatisfactoriamente();
            return $this->redirectToRoute('app_evento_calendario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evento_calendario/new.html.twig', [
            'evento_calendario' => $eventoCalendario,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_evento_calendario_show", methods={"GET"})
     */
    public function show(EventoCalendario $eventoCalendario): Response
    {
        return $this->render('evento_calendario/show.html.twig', [
            'evento_calendario' => $eventoCalendario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_evento_calendario_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EventoCalendario $eventoCalendario, EventoCalendarioRepository $eventoCalendarioRepository): Response
    {
        $form = $this->createForm(EventoCalendarioType::class, $eventoCalendario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventoCalendarioRepository->add($eventoCalendario, $this->getUser(), true);

            $this->addFlashModificadoSatisfactoriamente();
            return $this->redirectToRoute('app_evento_calendario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evento_calendario/edit.html.twig', [
            'evento_calendario' => $eventoCalendario,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_evento_calendario_delete", methods={"POST"})
     */
    public function delete(Request $request, EventoCalendario $eventoCalendario, EventoCalendarioRepository $eventoCalendarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eventoCalendario->getId(), $request->request->get('_token'))) {
            $eventoCalendarioRepository->remove($eventoCalendario, true);
        }

        $this->addFlashEliminadoSatisfactoriamente();
        return $this->redirectToRoute('app_evento_calendario_index', [], Response::HTTP_SEE_OTHER);
    }
}
