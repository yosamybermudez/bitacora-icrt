<?php

namespace App\Controller;

use App\Entity\IncidenciaTipo;
use App\Form\IncidenciaTipoType;
use App\Repository\IncidenciaTipoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/incidencia-tipo")
 */
class IncidenciaTipoController extends AppController
{
    /**
     * @Route("/", name="app_incidencia_tipo_index", methods={"GET"})
     */
    public function index(IncidenciaTipoRepository $incidenciaTipoRepository): Response
    {
        return $this->render('incidencia_tipo/index.html.twig', [
            'incidencia_tipos' => $incidenciaTipoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_incidencia_tipo_new", methods={"GET", "POST"})
     */
    public function new(Request $request, IncidenciaTipoRepository $incidenciaTipoRepository): Response
    {
        $incidenciaTipo = new IncidenciaTipo();
        $form = $this->createForm(IncidenciaTipoType::class, $incidenciaTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $incidenciaTipoRepository->add($incidenciaTipo, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_incidencia_tipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('incidencia_tipo/new.html.twig', [
            'incidencia_tipo' => $incidenciaTipo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_incidencia_tipo_show", methods={"GET"})
     */
    public function show(IncidenciaTipo $incidenciaTipo): Response
    {
        return $this->render('incidencia_tipo/show.html.twig', [
            'incidencia_tipo' => $incidenciaTipo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_incidencia_tipo_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, IncidenciaTipo $incidenciaTipo, IncidenciaTipoRepository $incidenciaTipoRepository): Response
    {
        $form = $this->createForm(IncidenciaTipoType::class, $incidenciaTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $incidenciaTipoRepository->add($incidenciaTipo, $this->getUser(), true);
            $this->addUpdated();
            return $this->redirectToRoute('app_incidencia_tipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('incidencia_tipo/edit.html.twig', [
            'incidencia_tipo' => $incidenciaTipo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_incidencia_tipo_delete", methods={"POST"})
     */
    public function delete(Request $request, IncidenciaTipo $incidenciaTipo, IncidenciaTipoRepository $incidenciaTipoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$incidenciaTipo->getId(), $request->request->get('_token'))) {
            $incidenciaTipoRepository->remove($incidenciaTipo, true);
            $this->addDeleted();
        }

        return $this->redirectToRoute('app_incidencia_tipo_index', [], Response::HTTP_SEE_OTHER);
    }
}
