<?php

namespace App\Controller;

use App\Entity\Trabajador;
use App\Form\TrabajadorType;
use App\Repository\TareaEspecificaRepository;
use App\Repository\TareaRepository;
use App\Repository\TrabajadorRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Cropperjs\Factory\CropperInterface;
use Symfony\UX\Cropperjs\Form\CropperType;

/**
 * @Route("/trabajador")
 */
class TrabajadorController extends AppController
{
    /**
     * @Route("/", name="app_trabajador_index", methods={"GET"})
     */
    public function index(TrabajadorRepository $trabajadorRepository): Response
    {
        return $this->render('trabajador/index.html.twig', [
            'trabajadores' => $trabajadorRepository->findBy([],['nombres' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="app_trabajador_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TrabajadorRepository $trabajadorRepository): Response
    {
        $trabajador = new Trabajador();
        $form = $this->createForm(TrabajadorType::class, $trabajador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $redirect_to = $request->query->get('redirect_to');
            try{
                $trabajadorRepository->add($trabajador, $this->getUser(), true);
                $this->addCreated();
                return $redirect_to ? $this->redirect($redirect_to) : $this->redirectToRoute('app_trabajador_show', ['id' => $trabajador->getId()]);
            } catch (UniqueConstraintViolationException $e){
                $this->addCreatedError();
                if($this->isGranted('ROLE_ADMINISTRADOR_SISTEMA')){
                    $this->addCustomError($e->getMessage());
                }
            }
        }

        return $this->renderForm('trabajador/new.html.twig', [
            'trabajador' => $trabajador,
            'trabajadores' => $trabajadorRepository->findAll(),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/mis-tareas", name="app_trabajador_mis_tareas", methods={"GET"})
     */
    public function misTareas(TareaEspecificaRepository $tareaEspecificaRepository): Response
    {
        $tareas = $tareaEspecificaRepository->findTareasPendientes($this->getUser(),$this->isGranted('ROLE_JEFE_INFORMATICA'));
        return $this->render('trabajador/mis_tareas.html.twig', [
            'tareas' => $tareas,
        ]);
    }

    /**
     * @Route("/{id}", name="app_trabajador_show", methods={"GET"})
     */
    public function show(Trabajador $trabajador): Response
    {
        return $this->render('trabajador/show.html.twig', [
            'trabajador' => $trabajador,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_trabajador_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CropperInterface $cropper, Trabajador $trabajador, TrabajadorRepository $trabajadorRepository): Response
    {
        $form = $this->createForm(TrabajadorType::class, $trabajador);
//        $crop = $cropper->createCrop('/server/path/to/the/image.jpg');
//        $crop->setCroppedMaxSize(2000, 1500);
//
//        $form
//            ->add('crop', CropperType::class, [
//                'public_url' => '/public/url/to/the/image.jpg',
//                'cropper_options' => [
//                    'aspectRatio' => 2000 / 1500,
//                ],
//                'mapped' => false
//            ])
//        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try{
                $trabajadorRepository->add($trabajador, $this->getUser(), true);
                $this->addUpdated();
                return $this->redirectToRoute('app_trabajador_show', ['id' => $trabajador->getId()], Response::HTTP_SEE_OTHER);
            }
            catch (UniqueConstraintViolationException $e){
                $this->flasher->addError('El usuario seleccionado está asignado a otro trabajador');
            }

        }
        return $this->renderForm('trabajador/new.html.twig', [
            'trabajador' => $trabajador,
            'trabajadores' => $trabajadorRepository->findAll(),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_trabajador_delete", methods={"POST"})
     */
    public function delete(Request $request, Trabajador $trabajador, TrabajadorRepository $trabajadorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trabajador->getId(), $request->request->get('_token'))) {
            $trabajadorRepository->remove($trabajador, true);
        }

        $this->addDeleted();
        return $this->redirectToRoute('app_trabajador_index', [], Response::HTTP_SEE_OTHER);
    }
}
