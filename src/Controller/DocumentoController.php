<?php

namespace App\Controller;

use App\Entity\Documento;
use App\Form\DocumentoType;
use App\Repository\DocumentoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/documento")
 */
class DocumentoController extends AppController
{
    /**
     * @Route("/", name="app_documento_index", methods={"GET"})
     */
    public function index(DocumentoRepository $documentoRepository): Response
    {
        return $this->render('documento/index.html.twig', [
            'documentos' => $documentoRepository->findAll([],['titulo' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="app_documento_new", methods={"GET", "POST"})
     */
    public function new(Request $request, DocumentoRepository $documentoRepository): Response
    {
        $documento = new Documento();
        $form = $this->createForm(DocumentoType::class, $documento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentoRepository->add($documento, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_documento_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('documento/new.html.twig', [
            'documento' => $documento,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_documento_show", methods={"GET"})
     */
    public function show(Documento $documento): Response
    {
        return $this->render('documento/show.html.twig', [
            'documento' => $documento,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_documento_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Documento $documento, DocumentoRepository $documentoRepository): Response
    {
        $form = $this->createForm(DocumentoType::class, $documento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentoRepository->add($documento, $this->getUser(), true);
            $this->addUpdated();
            return $this->redirectToRoute('app_documento_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('documento/new.html.twig', [
            'documento' => $documento,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_documento_delete", methods={"POST"})
     */
    public function delete(Request $request, Documento $documento, DocumentoRepository $documentoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$documento->getId(), $request->request->get('_token'))) {
            $documentoRepository->remove($documento, true);
            $this->addDeleted();
        }

        return $this->redirectToRoute('app_documento_index', [], Response::HTTP_SEE_OTHER);
    }
}
