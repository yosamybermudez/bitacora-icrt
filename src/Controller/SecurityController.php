<?php

namespace App\Controller;

use App\Form\UsuarioType;
use App\Repository\UsuarioRepository;
use App\Security\ActiveDirectoryService;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AppController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, UsuarioRepository $usuarioRepository, ActiveDirectoryService $directory): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error){
            $usuario = $usuarioRepository->findOneByUsername($lastUsername);
            if(!$usuario){
                $this->addCustomError(sprintf('El usuario "%s" no existe', $lastUsername));
            } else {
                $this->addCustomError('Credenciales no válidas');
            }
        }

        try{

            $directory->bind();
            $domain_fqdn = $directory->getDomainFqdn();
        } catch (\Exception $e)
        {
            $domain_fqdn = null;
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'domain_fqdn' => $domain_fqdn]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/profile", name="app_user_profile", methods={"GET"})
     */
    public function userProfile(): Response
    {
        return $this->render('usuario/profile.html.twig', [
            'usuario' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/change-password", name="app_change_password", methods={"GET", "POST"})
     */
    public function changePassword(Request $request, UsuarioRepository $usuarioRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $usuario = $usuarioRepository->findOneBy(['username' => $this->getUser()->getUserIdentifier()]);
        $form = $this->createForm(UsuarioType::class, $usuario, ['mapped' => false]);
        $form->remove('datos_generales');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
            $this->addCustomSuccess('Contraseña cambiada satisfactoriamente');
            $usuarioRepository->add($usuario, $this->getUser(), true);
            return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/change_password.html.twig', [
            'usuario' => $usuario,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/forgot_password", name="app_forgot_password", methods={"GET"})
     */
    public function forgotPassword(): Response
    {
        return $this->render('security/forgot_password.html.twig', [

        ]);
    }

    /**
     * @Route("/error/{code}", name="app_error", methods={"GET"})
     */
    public function error(FlattenException $exception): Response
    {
        $this->addCustomError($exception->getMessage());
        return $this->render('security/404.html.twig', [

        ]);
    }
}
