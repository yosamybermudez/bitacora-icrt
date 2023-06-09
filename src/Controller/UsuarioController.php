<?php

namespace App\Controller;

use App\Entity\Trabajador;
use App\Entity\Usuario;
use App\Form\UsuarioType;
use App\Repository\AppTemaRepository;
use App\Repository\AreaRepository;
use App\Repository\RolRepository;
use App\Repository\TrabajadorRepository;
use App\Repository\UsuarioRepository;
use App\Service\BotTelegram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/usuario")
 */
class UsuarioController extends AppController
{
    /**
     * @Route("/", name="app_usuario_index", methods={"GET"})
     */
    public function index(UsuarioRepository $usuarioRepository, AreaRepository $areaRepository): Response
    {
        return $this->render('usuario/index.html.twig', [
            'usuarios' => $usuarioRepository->findAll(),
            'areas' => $areaRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="app_usuario_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UsuarioRepository $usuarioRepository, TrabajadorRepository $trabajadorRepository, UserPasswordHasherInterface $passwordHasher): Response
    {

        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario, ['required_password' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trabajador = $request->request->get('usuario')['datos_generales']['trabajador'];
            if($trabajador !== null){
                $trabajador = $trabajadorRepository->find($trabajador);
                $usuario->setTrabajador($trabajador);
            }
            try{
                $password = $request->request->get('usuario')['cambiar_contrasena']['password']['first'];
            } catch (\Exception $e)
            {
                $password = null;
            }
            if($password !== null){
                $hashedPassword = $passwordHasher->hashPassword(
                    $usuario,
                    $password
                );
                $usuario->setPassword($hashedPassword);
            }
            $usuarioRepository->add($usuario, $this->getUser(), true);
            $this->addCreated();
            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_usuario_show", methods={"GET"})
     */
    public function show(Usuario $usuario): Response
    {
        return $this->render('usuario/show.html.twig', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_usuario_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Usuario $usuario, UsuarioRepository $usuarioRepository, TrabajadorRepository $trabajadorRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UsuarioType::class, $usuario, ['mapped' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trabajador = $request->request->get('usuario')['datos_generales']['trabajador'];
            if($trabajador !== null){
                $trabajador = $trabajadorRepository->find($trabajador);
                $usuario->setTrabajador($trabajador);
            }
            try{
                $password = $request->request->get('usuario')['cambiar_contrasena']['password']['first'];
            } catch (\Exception $e)
            {
                $password = null;
            }
            if($password !== null){
                $hashedPassword = $passwordHasher->hashPassword(
                    $usuario,
                    $password
                );
                $usuario->setPassword($hashedPassword);
            }
            $this->addUpdated();
            $usuarioRepository->add($usuario, $this->getUser(), true);
            return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('usuario/new.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_usuario_delete", methods={"POST"})
     */
    public function delete(Request $request, Usuario $usuario, UsuarioRepository $usuarioRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usuario->getId(), $request->request->get('_token'))) {
            $usuarioRepository->remove($usuario, true);
        }
        $this->addFlashEliminadoSatisfactoriamente();
        return $this->redirectToRoute('app_usuario_index', [], Response::HTTP_SEE_OTHER);
    }

}
