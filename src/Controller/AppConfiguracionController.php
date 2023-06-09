<?php

namespace App\Controller;

use App\Entity\AppConfiguracion;
use App\Form\AppConfiguracionType;
use App\Repository\AppConfiguracionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/configuracion")
 */
class AppConfiguracionController extends AbstractController
{
    /**
     * @Route("/", name="app_configuracion_index", methods={"GET"})
     */
    public function index(AppConfiguracionRepository $appConfiguracionRepository): Response
    {
        return $this->render('app_configuracion/index.html.twig', [
            'app_configuracions' => $appConfiguracionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_configuracion_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AppConfiguracionRepository $appConfiguracionRepository): Response
    {
        $appConfiguracion = new AppConfiguracion();
        $form = $this->createForm(AppConfiguracionType::class, $appConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appConfiguracionRepository->add($appConfiguracion, true);

            return $this->redirectToRoute('app_configuracion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('app_configuracion/new.html.twig', [
            'app_configuracion' => $appConfiguracion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_configuracion_show", methods={"GET"})
     */
    public function show(AppConfiguracion $appConfiguracion): Response
    {
        return $this->render('app_configuracion/show.html.twig', [
            'app_configuracion' => $appConfiguracion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_configuracion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, AppConfiguracion $appConfiguracion, AppConfiguracionRepository $appConfiguracionRepository): Response
    {
        $form = $this->createForm(AppConfiguracionType::class, $appConfiguracion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appConfiguracionRepository->add($appConfiguracion, true);

            return $this->redirectToRoute('app_configuracion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('app_configuracion/new.html.twig', [
            'app_configuracion' => $appConfiguracion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_configuracion_delete", methods={"POST"})
     */
    public function delete(Request $request, AppConfiguracion $appConfiguracion, AppConfiguracionRepository $appConfiguracionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appConfiguracion->getId(), $request->request->get('_token'))) {
            $appConfiguracionRepository->remove($appConfiguracion, true);
        }

        return $this->redirectToRoute('app_configuracion_index', [], Response::HTTP_SEE_OTHER);
    }
}
