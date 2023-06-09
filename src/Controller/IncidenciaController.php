<?php

namespace App\Controller;

use App\Entity\Incidencia;
use App\Entity\TareaTipo;
use App\Form\IncidenciaCredencialType;
use App\Form\IncidenciaType;
use App\Repository\CredencialRepository;
use App\Repository\IncidenciaRepository;
use App\Repository\TareaTipoRepository;
use App\Repository\UsuarioRepository;
use App\Service\FileUploader;
use Flasher\Prime\FlasherInterface;
use Flasher\Toastr\Prime\ToastrFactory;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use PharIo\Manifest\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

/**
 * @Route("/incidencia")
 */
class IncidenciaController extends AppController
{

    /**
     * @Route("/", name="app_incidencia_index", methods={"GET"})
     */
    public function index(IncidenciaRepository $incidenciaRepository): Response
    {
        return $this->render('incidencia/index.html.twig', [
            'incidencias' => $incidenciaRepository->findIncidenciasPorArea($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA')),
            'mostrar_estado' => true
        ]);
    }

    /**
     * @Route("/no_solucionada", name="app_incidencia_no_solucionada", methods={"GET"})
     */
    public function noSolucionadas(IncidenciaRepository $incidenciaRepository): Response
    {

        $incidencias = $incidenciaRepository->findIncidenciasNoSolucionadasPorArea($this->getUser(), $this->isGranted('ROLE_JEFE_INFORMATICA'));

        return $this->render('incidencia/index.html.twig', [
            'incidencias' => $incidencias,
        ]);
    }

    /**
     * @Route("/new", name="app_incidencia_new", methods={"GET", "POST"})
     */
    public function new(Request $request, IncidenciaRepository $incidenciaRepository): Response
    {
        $incidencium = new Incidencia();
        $form = $this->createForm(IncidenciaType::class, $incidencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($incidencium->getEstado() === 'pendiente'){
                $incidencium->setSolucion(null);
                $incidencium->setAdjuntoSolucion(null);
                $incidencium->setAdjuntoSolucionFile(null);
            }
            $etiquetas = $request->request->get('incidencia')['problema_form']['etiquetas'];
            $incidencium->setEtiquetas(explode(',',$etiquetas));
            $incidenciaRepository->add($incidencium, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_incidencia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('incidencia/new.html.twig', [
            'incidencium' => $incidencium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new/cambio_credencial", name="app_incidencia_new_cambio_credencial", methods={"GET", "POST"})
     */
    public function newCambioCredencial(Request $request, IncidenciaRepository $incidenciaRepository, CredencialRepository $credencialRepository, TareaTipoRepository $tareaTipoRepository): Response
    {

        $form = $this->createForm(IncidenciaCredencialType::class);
        $incidencia = null;
        $incidencia_request = $request->query->get('incidencia');
        if($incidencia_request !== null){
            $incidencia = $incidenciaRepository->find($incidencia_request);
            if($incidencia && $incidencia->getCredencial()) {
                $form->get('credencial')->setData($incidencia->getCredencial());
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $request_array = $request->request->get('incidencia_credencial');
            $credencial = $credencialRepository->find($request_array['credencial']);
            $credencial->setPassword($request_array['password']['first']);
            $credencialRepository->add($credencial, $this->getUser(), true);

            $task = 'actualizó';
            if(!$incidencia){
                $incidencia = new Incidencia();
                $incidencia->setTitulo(sprintf('Cambio de contraseña: %s', $credencial->getDestino()));
                $incidencia->setProblema(sprintf('Necesidad de cambiar la contraseña de: %s', $credencial->getDestino()));
                foreach ($credencial->getAreas() as $area){
                    $incidencia->addArea($area);
                }
                $task = 'registró';
            }
            $incidencia->setEtiquetas(array_unique(array_merge($incidencia->getEtiquetas(), ['Cambio de contraseña'])));
            $incidencia->setEstado('solucionada');
            $incidencia->setSolucion('Se cambio la contraseña correctamente');

            $incidenciaRepository->add($incidencia, $this->getUser(), true);
            $this->addCustomSuccess(sprintf('Se cambió correctamente la credencial seleccionada y se %s la correspondiente incidencia', $task));
            return $this->redirectToRoute('app_credencial_show', ['id' => $credencial->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('incidencia/cambio_credencial.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_incidencia_show", methods={"GET"})
     */
    public function show(Incidencia $incidencium): Response
    {
        return $this->render('incidencia/show.html.twig', [
            'incidencia' => $incidencium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_incidencia_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Incidencia $incidencium, IncidenciaRepository $incidenciaRepository, MailerInterface $mailer, TransportInterface $transport): Response
    {

        $form = $this->createForm(IncidenciaType::class, $incidencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etiquetas = $request->request->get('etiquetas_hidden');
            $incidencium->setEtiquetas(explode(',',$etiquetas));
            $incidenciaRepository->add($incidencium, $this->getUser(), true);
            $this->addUpdated();
//            $email = (new \Symfony\Component\Mime\Email())
//                ->to('samy@icrt.cu')
//                ->from('admin@icrt.cu')
//                ->text('Mensaje de prueba')
//                ->subject('Incidencia editada')
//                ->html('<p>See Twig integration for better HTML integration!</p>');
//            $this->enviarCorreoElectronico($email);
            return $this->redirectToRoute('app_incidencia_show', ['id' => $incidencium->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('incidencia/new.html.twig', [
            'incidencium' => $incidencium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/registar_solucion", name="app_incidencia_registrar_solucion", methods={"GET", "POST"})
     */
    public function registarSolucion(Request $request, Incidencia $incidencium, IncidenciaRepository $incidenciaRepository): Response
    {
        $form = $this->createForm(IncidenciaType::class, $incidencium);
        $form->remove('problema_form');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $incidencium->setEstado('solucionada');
            $incidenciaRepository->add($incidencium, $this->getUser(), true);
            $this->addUpdated();
            return $this->redirectToRoute('app_incidencia_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('incidencia/solucion.html.twig', [
            'incidencia' => $incidencium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_incidencia_delete", methods={"POST"})
     */
    public function delete(Request $request, Incidencia $incidencium, IncidenciaRepository $incidenciaRepository, FlasherInterface $flasher): Response
    {
        if ($this->isCsrfTokenValid('delete'.$incidencium->getId(), $request->request->get('_token'))) {
            $incidenciaRepository->remove($incidencium, true);
        }

        $this->addDeleted();
        return $this->redirectToRoute('app_incidencia_index', [], Response::HTTP_SEE_OTHER);
    }
}
