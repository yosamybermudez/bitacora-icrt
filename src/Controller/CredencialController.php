<?php

namespace App\Controller;

use App\Entity\Credencial;
use App\Form\CredencialType;
use App\Repository\CredencialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credencial")
 */
class CredencialController extends AppController
{
    /**
     * @Route("/", name="app_credencial_index", methods={"GET"})
     */
    public function index(CredencialRepository $credencialRepository): Response
    {
        return $this->render('credencial/index.html.twig', [
            'credenciales' => $credencialRepository->findCredencialesPorArea($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA')),
        ]);
    }

    /**
     * @Route("/new", name="app_credencial_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CredencialRepository $credencialRepository): Response
    {
        $credencial = new Credencial();

        $form = $this->createForm(CredencialType::class, $credencial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $credencial->setCreadoPor($this->getUser());
            $credencial->setCreadoEn(new \DateTime());
            $credencial->setActualizadoPor($this->getUser());
            $credencial->setActualizadoEn(new \DateTime());

            $credencialRepository->add($credencial, $this->getUser(), true);

            $this->addCreated();
            return $this->redirectToRoute('app_credencial_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('credencial/new.html.twig', [
            'credencial' => $credencial,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_credencial_show", methods={"GET"})
     */
    public function show(Credencial $credencial): Response
    {
        return $this->render('credencial/show.html.twig', [
            'credencial' => $credencial,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_credencial_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Credencial $credencial, CredencialRepository $credencialRepository): Response
    {
        $form = $this->createForm(CredencialType::class, $credencial, ['required_password' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ips = $request->request->get('ips_hidden');
            if($ips){
                $credencial->setIps(explode(',', $ips));
            }
            $credencialRepository->add($credencial, $this->getUser(), true);
            $this->flasher->addUpdated();
            return $this->redirectToRoute('app_credencial_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('credencial/new.html.twig', [
            'credencial' => $credencial,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_credencial_delete", methods={"POST"})
     */
    public function delete(Request $request, Credencial $credencial, CredencialRepository $credencialRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$credencial->getId(), $request->request->get('_token'))) {
            $credencialRepository->remove($credencial, true);
        }
        $this->addDeleted();
        return $this->redirectToRoute('app_credencial_index', [], Response::HTTP_SEE_OTHER);
    }
}
