<?php

namespace App\Controller;

use App\Entity\TareaTipo;
use App\Form\TareaTipoType;
use App\Repository\TareaTipoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tarea-tipo")
 */
class TareaTipoController extends AppController
{
    /**
     * @Route("/", name="app_tarea_tipo_index", methods={"GET"})
     */
    public function index(TareaTipoRepository $tareaTipoRepository): Response
    {
        return $this->render('tarea_tipo/index.html.twig', [
            'tarea_tipos' => $tareaTipoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_tarea_tipo_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TareaTipoRepository $tareaTipoRepository): Response
    {
        $tareaTipo = new TareaTipo();
        $form = $this->createForm(TareaTipoType::class, $tareaTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tareaTipoRepository->add($tareaTipo, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_tarea_tipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarea_tipo/new.html.twig', [
            'tarea_tipo' => $tareaTipo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarea_tipo_show", methods={"GET"})
     */
    public function show(TareaTipo $tareaTipo): Response
    {
        return $this->render('tarea_tipo/show.html.twig', [
            'tarea_tipo' => $tareaTipo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_tarea_tipo_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TareaTipo $tareaTipo, TareaTipoRepository $tareaTipoRepository): Response
    {
        $form = $this->createForm(TareaTipoType::class, $tareaTipo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tareaTipoRepository->add($tareaTipo, $this->getUser(), true);
            $this->addUpdated();
            return $this->redirectToRoute('app_tarea_tipo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarea_tipo/new.html.twig', [
            'tarea_tipo' => $tareaTipo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarea_tipo_delete", methods={"POST"})
     */
    public function delete(Request $request, TareaTipo $tareaTipo, TareaTipoRepository $tareaTipoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tareaTipo->getId(), $request->request->get('_token'))) {
            $tareaTipoRepository->remove($tareaTipo, true);
        }
        $this->addDeleted();
        return $this->redirectToRoute('app_tarea_tipo_index', [], Response::HTTP_SEE_OTHER);
    }
}
