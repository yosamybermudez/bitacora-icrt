<?php

namespace App\Controller;

use App\Entity\Cargo;
use App\Form\CargoType;
use App\Repository\CargoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cargo")
 */
class CargoController extends AppController
{
    /**
     * @Route("/", name="app_cargo_index", methods={"GET"})
     */
    public function index(CargoRepository $cargoRepository): Response
    {
        return $this->render('cargo/index.html.twig', [
            'cargos' => $cargoRepository->findBy([], ['nombre' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="app_cargo_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CargoRepository $cargoRepository): Response
    {
        $cargo = new Cargo();
        $form = $this->createForm(CargoType::class, $cargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cargoRepository->add($cargo, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_cargo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cargo/new.html.twig', [
            'cargo' => $cargo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cargo_show", methods={"GET"})
     */
    public function show(Cargo $cargo): Response
    {
        return $this->render('cargo/show.html.twig', [
            'cargo' => $cargo,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_cargo_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Cargo $cargo, CargoRepository $cargoRepository): Response
    {
        $form = $this->createForm(CargoType::class, $cargo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cargoRepository->add($cargo, $this->getUser(), true);
            $this->flasher->addUpdated();
            return $this->redirectToRoute('app_cargo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cargo/edit.html.twig', [
            'cargo' => $cargo,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cargo_delete", methods={"POST"})
     */
    public function delete(Request $request, Cargo $cargo, CargoRepository $cargoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cargo->getId(), $request->request->get('_token'))) {
            $cargoRepository->remove($cargo, true);
        }
        $this->addDeleted();
        return $this->redirectToRoute('app_cargo_index', [], Response::HTTP_SEE_OTHER);
    }
}
