<?php

namespace App\Controller;

use App\Entity\FechaConmemorativa;
use App\Form\FechaConmemorativaType;
use App\Repository\FechaConmemorativaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/fecha/conmemorativa")
 */
class FechaConmemorativaController extends AbstractController
{
    /**
     * @Route("/", name="app_fecha_conmemorativa_index", methods={"GET"})
     */
    public function index(FechaConmemorativaRepository $fechaConmemorativaRepository): Response
    {
        return $this->render('fecha_conmemorativa/index.html.twig', [
            'fecha_conmemorativas' => $fechaConmemorativaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_fecha_conmemorativa_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FechaConmemorativaRepository $fechaConmemorativaRepository): Response
    {
        $fechaConmemorativa = new FechaConmemorativa();
        $form = $this->createForm(FechaConmemorativaType::class, $fechaConmemorativa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fechaConmemorativaRepository->add($fechaConmemorativa, true);

            return $this->redirectToRoute('app_fecha_conmemorativa_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fecha_conmemorativa/new.html.twig', [
            'fecha_conmemorativa' => $fechaConmemorativa,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_fecha_conmemorativa_show", methods={"GET"})
     */
    public function show(FechaConmemorativa $fechaConmemorativa): Response
    {
        return $this->render('fecha_conmemorativa/show.html.twig', [
            'fecha_conmemorativa' => $fechaConmemorativa,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_fecha_conmemorativa_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FechaConmemorativa $fechaConmemorativa, FechaConmemorativaRepository $fechaConmemorativaRepository): Response
    {
        $form = $this->createForm(FechaConmemorativaType::class, $fechaConmemorativa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fechaConmemorativaRepository->add($fechaConmemorativa, true);

            return $this->redirectToRoute('app_fecha_conmemorativa_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('fecha_conmemorativa/edit.html.twig', [
            'fecha_conmemorativa' => $fechaConmemorativa,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_fecha_conmemorativa_delete", methods={"POST"})
     */
    public function delete(Request $request, FechaConmemorativa $fechaConmemorativa, FechaConmemorativaRepository $fechaConmemorativaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fechaConmemorativa->getId(), $request->request->get('_token'))) {
            $fechaConmemorativaRepository->remove($fechaConmemorativa, true);
        }

        return $this->redirectToRoute('app_fecha_conmemorativa_index', [], Response::HTTP_SEE_OTHER);
    }
}
