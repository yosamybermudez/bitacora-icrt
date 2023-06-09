<?php

namespace App\Controller;

use App\Entity\Area;
use App\Form\AreaType;
use App\Repository\AreaRepository;
use Flasher\Prime\FlasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/area")
 */
class AreaController extends AppController
{
    /**
     * @Route("/", name="app_area_index", methods={"GET"})
     */
    public function index(AreaRepository $areaRepository): Response
    {
        return $this->render('area/index.html.twig', [
            'areas' => $areaRepository->findBy([], ['nombre' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="app_area_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AreaRepository $areaRepository, FlasherInterface $flasher): Response
    {
        $area = new Area();
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $areaRepository->add($area, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('area/new.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_area_show", methods={"GET"})
     */
    public function show(Area $area): Response
    {
        return $this->render('area/show.html.twig', [
            'area' => $area,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_area_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Area $area, AreaRepository $areaRepository, FlasherInterface $flasher): Response
    {
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $areaRepository->add($area, $this->getUser(), true);
            $this->flasher->addUpdated();
            return $this->redirectToRoute('app_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('area/edit.html.twig', [
            'area' => $area,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_area_delete", methods={"POST"})
     */
    public function delete(Request $request, Area $area, AreaRepository $areaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$area->getId(), $request->request->get('_token'))) {
            try{
                $areaRepository->remove($area, true);
                $this->addDeleted();
            } catch (\Exception $e){
                $this->addDeletedError();
            }
        }
        return $this->redirectToRoute('app_area_index', [], Response::HTTP_SEE_OTHER);
    }
}
